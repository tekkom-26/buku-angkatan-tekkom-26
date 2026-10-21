<?php

namespace App\Filament\Pages;

use App\Models\StorageFile;
use App\Services\R2FileManagerService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

/**
 * Private / admin-only file manager over the "r2" disk. Browses every
 * `storage_files` record (both public and private visibility — admins can
 * see everything), supports uploading to a desired path, creating
 * directories, and deleting entries.
 */
class FileManager extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedFolder;

    protected static ?string $navigationLabel = 'File Manager';

    protected static ?string $title = 'File Manager (Cloudflare R2)';

    protected string $view = 'filament.pages.file-manager';

    /**
     * Current directory being browsed, relative to the disk root.
     */
    public string $currentDirectory = '';

    public function getCurrentDirectory(): string
    {
        return trim($this->currentDirectory, '/');
    }

    public function goToDirectory(string $path): void
    {
        $this->currentDirectory = trim($path, '/');
        $this->resetTable();
    }

    public function goUp(): void
    {
        $segments = array_filter(explode('/', $this->getCurrentDirectory()));
        array_pop($segments);
        $this->goToDirectory(implode('/', $segments));
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                StorageFile::query()->inDirectory($this->getCurrentDirectory())
            )
            ->columns([
                IconColumn::make('is_directory')
                    ->label('')
                    ->icon(fn (StorageFile $record) => $record->is_directory ? Heroicon::OutlinedFolder : Heroicon::OutlinedDocument)
                    ->color(fn (StorageFile $record) => $record->is_directory ? 'warning' : 'gray'),
                TextColumn::make('name')
                    ->label('Name')
                    ->getStateUsing(fn (StorageFile $record) => basename($record->path))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('path', 'like', "%{$search}%"))
                    ->weight(fn (StorageFile $record) => $record->is_directory ? 'bold' : null),
                TextColumn::make('visibility')
                    ->badge()
                    ->color(fn (string $state) => $state === StorageFile::VISIBILITY_PUBLIC ? 'success' : 'gray'),
                TextColumn::make('alias')
                    ->placeholder('—'),
                TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn (StorageFile $record) => $record->is_directory ? '' : $record->humanSize()),
                TextColumn::make('uploaded_by')
                    ->label('Uploaded by')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Uploaded at')
                    ->dateTime(),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Open')
                    ->icon(Heroicon::OutlinedFolderOpen)
                    ->visible(fn (StorageFile $record) => $record->is_directory)
                    ->action(fn (StorageFile $record) => $this->goToDirectory($record->path)),
                Action::make('download')
                    ->label('Download')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->visible(fn (StorageFile $record) => ! $record->is_directory)
                    ->url(fn (StorageFile $record) => $record->url(), shouldOpenInNewTab: true),
                Action::make('rename')
                    ->label('Rename / Move')
                    ->icon(Heroicon::OutlinedPencil)
                    ->schema([
                        TextInput::make('path')
                            ->label('New path')
                            ->default(fn (StorageFile $record) => $record->path)
                            ->required(),
                        TextInput::make('alias')
                            ->label('Alias')
                            ->default(fn (StorageFile $record) => $record->alias)
                            ->nullable(),
                    ])
                    ->action(function (StorageFile $record, array $data): void {
                        $service = app(R2FileManagerService::class);

                        try {
                            if ($data['path'] !== $record->path) {
                                $service->move($record, $data['path']);
                            }

                            if (($data['alias'] ?? null) !== $record->alias) {
                                $record->update(['alias' => $data['alias'] ?: null]);
                            }
                        } catch (InvalidArgumentException $exception) {
                            Notification::make()->danger()->title('Could not update')->body($exception->getMessage())->send();

                            return;
                        }

                        Notification::make()->success()->title('Updated')->send();
                    }),
                DeleteAction::make()
                    ->action(function (StorageFile $record): void {
                        app(R2FileManagerService::class)->delete($record);
                    }),
            ])
            ->emptyStateHeading('This folder is empty')
            ->emptyStateDescription('Upload a file or create a directory to get started.');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createDirectory')
                ->label('New Folder')
                ->icon(Heroicon::OutlinedFolderPlus)
                ->schema([
                    TextInput::make('name')
                        ->label('Folder name')
                        ->required(),
                    Select::make('visibility')
                        ->options([
                            StorageFile::VISIBILITY_PRIVATE => 'Private (admin only)',
                            StorageFile::VISIBILITY_PUBLIC => 'Public',
                        ])
                        ->default(StorageFile::VISIBILITY_PRIVATE)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $path = trim($this->getCurrentDirectory().'/'.$data['name'], '/');

                    try {
                        app(R2FileManagerService::class)->createDirectory(
                            path: $path,
                            visibility: $data['visibility'],
                            createdBy: filament()->auth()->user()?->email,
                        );
                    } catch (InvalidArgumentException $exception) {
                        throw ValidationException::withMessages(['name' => $exception->getMessage()]);
                    }

                    Notification::make()->success()->title('Folder created')->send();
                    $this->resetTable();
                }),
            Action::make('upload')
                ->label('Upload File')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->schema([
                    FileUpload::make('file')
                        ->label('File')
                        ->required()
                        ->disk('local')
                        ->directory('tmp-uploads')
                        ->visibility('private')
                        ->storeFileNamesIn('original_filename')
                        ->maxSize(51200), // 50 MB, local staging only
                    TextInput::make('filename')
                        ->label('Save as (optional — defaults to original filename)')
                        ->helperText('Saved inside the folder currently open. File extension is kept automatically if you leave it out.'),
                    TextInput::make('alias')
                        ->label('Alias (optional)'),
                    Select::make('visibility')
                        ->options([
                            StorageFile::VISIBILITY_PRIVATE => 'Private (admin only)',
                            StorageFile::VISIBILITY_PUBLIC => 'Public',
                        ])
                        ->default(StorageFile::VISIBILITY_PRIVATE)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $tmpDisk = \Illuminate\Support\Facades\Storage::disk('local');
                    $tmpPath = $data['file'];
                    $absoluteTmpPath = $tmpDisk->path($tmpPath);

                    $originalName = $data['original_filename'] ?? basename($tmpPath);
                    $desiredName = $data['filename'] ?: $originalName;

                    // The R2FileManagerService treats any path segment without a
                    // dot-extension as a *directory* to upload into (see
                    // resolveUploadPath()). Since this form already separates
                    // "current folder" (navigation) from "file name" (this
                    // field), always make sure the typed name keeps a file
                    // extension — otherwise "myphoto" would silently become a
                    // folder named "myphoto" containing a randomly named file.
                    if (pathinfo($desiredName, PATHINFO_EXTENSION) === '') {
                        $originalExtension = pathinfo($originalName, PATHINFO_EXTENSION);

                        if ($originalExtension !== '') {
                            $desiredName .= '.'.$originalExtension;
                        }
                    }

                    $directory = $this->getCurrentDirectory();

                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $absoluteTmpPath,
                        $desiredName,
                        $tmpDisk->mimeType($tmpPath),
                        null,
                        true,
                    );

                    try {
                        app(R2FileManagerService::class)->upload(
                            file: $uploadedFile,
                            desiredPath: ($directory !== '' ? $directory.'/' : '').$desiredName,
                            visibility: $data['visibility'],
                            alias: $data['alias'] ?: null,
                            uploadedBy: filament()->auth()->user()?->email,
                        );
                    } catch (InvalidArgumentException $exception) {
                        throw ValidationException::withMessages(['filename' => $exception->getMessage()]);
                    } finally {
                        $tmpDisk->delete($tmpPath);
                    }

                    Notification::make()->success()->title('File uploaded')->send();
                    $this->resetTable();
                }),
        ];
    }
}

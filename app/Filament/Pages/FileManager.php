<?php

namespace App\Filament\Pages;

use App\Models\StorageFile;
use App\Services\R2FileManagerService;
use App\Services\R2FileOperationException;
use Filament\Actions\Action;
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
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

/**
 * Private / admin-only file manager over the "r2" disk. Browses every
 * `storage_files` record (both public and private visibility — admins can
 * see everything), supports uploading to a desired path, creating
 * virtual directories, and deleting entries.
 */
class FileManager extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

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
            ->records(fn (?string $search, int $page, int $recordsPerPage): LengthAwarePaginator => $this->tableRecords(
                $search,
                $page,
                $recordsPerPage,
            ))
            ->columns([
                IconColumn::make('is_directory')
                    ->label('')
                    ->icon(fn (array $record) => $record['is_directory'] ? Heroicon::OutlinedFolder : Heroicon::OutlinedDocument)
                    ->color(fn (array $record) => $record['is_directory'] ? 'warning' : 'gray'),
                TextColumn::make('name')
                    ->label('Name')
                    ->state(fn (array $record): string => basename($record['path']))
                    ->weight(fn (array $record) => $record['is_directory'] ? 'bold' : null),
                TextColumn::make('visibility')
                    ->badge()
                    ->color(fn (?string $state) => $state === StorageFile::VISIBILITY_PUBLIC ? 'success' : 'gray'),
                TextColumn::make('alias')
                    ->placeholder('—'),
                TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn (array $record) => $record['is_directory'] ? '' : $record['human_size']),
                TextColumn::make('uploaded_by')
                    ->label('Uploaded by')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Uploaded at')
                    ->dateTime(),
            ])
            ->searchable()
            ->recordActions([
                Action::make('open')
                    ->label('Open')
                    ->icon(Heroicon::OutlinedFolderOpen)
                    ->visible(fn (array $record) => $record['is_directory'])
                    ->action(fn (array $record) => $this->goToDirectory($record['path'])),
                Action::make('download')
                    ->label('Download')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->visible(fn (array $record) => ! $record['is_directory'])
                    ->url(fn (array $record): ?string => $record['url'], shouldOpenInNewTab: true),
                Action::make('rename')
                    ->label('Rename / Move')
                    ->icon(Heroicon::OutlinedPencil)
                    ->schema([
                        TextInput::make('path')
                            ->label('New path')
                            ->default(fn (array $record): string => $record['path'])
                            ->required(),
                        TextInput::make('alias')
                            ->label('Alias')
                            ->default(fn (array $record) => $record['alias'])
                            ->nullable()
                            ->visible(fn (array $record) => ! $record['is_directory']),
                    ])
                    ->action(function (array $record, array $data): void {
                        $service = app(R2FileManagerService::class);

                        try {
                            if ($data['path'] !== $record['path']) {
                                if ($record['is_directory']) {
                                    $result = $service->moveDirectory($record['path'], $data['path']);
                                    $this->notifyBatchResult('Folder moved', $result['moved'], $result['failed']);
                                } else {
                                    $file = $this->findFileRecord($record['id']);
                                    $service->move($file, $data['path']);
                                }
                            }

                            if (! $record['is_directory'] && ($data['alias'] ?? null) !== $record['alias']) {
                                $this->findFileRecord($record['id'])->update(['alias' => $data['alias'] ?: null]);
                            }
                        } catch (R2FileOperationException $exception) {
                            $this->notifyBatchResult('Folder move partially completed', $exception->results['moved'], $exception->results['failed']);
                        } catch (InvalidArgumentException $exception) {
                            Notification::make()->danger()->title('Could not update')->body($exception->getMessage())->send();

                            return;
                        }

                        if (! $record['is_directory']) {
                            Notification::make()->success()->title('Updated')->send();
                        }

                        $this->resetTable();
                    }),
                Action::make('delete')
                    ->label('Delete')
                    ->icon(Heroicon::OutlinedTrash)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (array $record): void {
                        $service = app(R2FileManagerService::class);

                        if ($record['is_directory']) {
                            try {
                                $result = $service->deleteDirectory($record['path']);
                                $this->notifyBatchResult('Folder deleted', $result['deleted'], $result['failed']);
                            } catch (R2FileOperationException $exception) {
                                $this->notifyBatchResult('Folder deletion partially completed', $exception->results['deleted'], $exception->results['failed']);
                            }
                        } else {
                            $service->delete($this->findFileRecord($record['id']));
                            Notification::make()->success()->title('File deleted')->send();
                        }

                        $this->resetTable();
                    }),
            ])
            ->emptyStateHeading('This folder is empty')
            ->emptyStateDescription('Upload a file to create a folder path.');
    }

    protected function getHeaderActions(): array
    {
        return [
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
                    $tmpDisk = Storage::disk('local');
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

                    $uploadedFile = new UploadedFile(
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

    /** @return LengthAwarePaginator<int, array<string, mixed>> */
    protected function tableRecords(?string $search, int $page, int $recordsPerPage): LengthAwarePaginator
    {
        $records = app(R2FileManagerService::class)
            ->listDirectory($this->getCurrentDirectory())
            ->mapWithKeys(function (StorageFile|array $item): array {
                if (is_array($item)) {
                    $record = [
                        'id' => 'directory:'.$item['path'],
                        'path' => $item['path'],
                        'is_directory' => true,
                        'visibility' => null,
                        'alias' => null,
                        'size' => null,
                        'human_size' => null,
                        'uploaded_by' => null,
                        'created_at' => null,
                        'url' => null,
                    ];

                    return [$record['id'] => $record];
                }

                $record = [
                    'id' => $item->getKey(),
                    'path' => $item->path,
                    'is_directory' => false,
                    'visibility' => $item->visibility,
                    'alias' => $item->alias,
                    'size' => $item->size,
                    'human_size' => $item->humanSize(),
                    'uploaded_by' => $item->uploaded_by,
                    'created_at' => $item->created_at?->toIso8601String(),
                    'url' => $item->url(),
                ];

                return [(string) $record['id'] => $record];
            })
            ->filter(fn (array $record): bool => ! filled($search)
                || str_contains(mb_strtolower($record['path']), mb_strtolower($search))
                || str_contains(mb_strtolower((string) $record['alias']), mb_strtolower($search)));

        return new LengthAwarePaginator(
            items: $records->forPage($page, $recordsPerPage),
            total: $records->count(),
            perPage: $recordsPerPage,
            currentPage: $page,
        );
    }

    protected function findFileRecord(string $id): StorageFile
    {
        return StorageFile::query()
            ->where('disk', 'r2')
            ->findOrFail($id);
    }

    /** @param array<int, mixed> $completed @param array<int, array<string, string>> $failed */
    protected function notifyBatchResult(string $title, array $completed, array $failed): void
    {
        $notification = Notification::make()
            ->title($title)
            ->body(count($completed).' completed; '.count($failed).' failed.');

        if ($failed !== []) {
            $notification->warning();
        } else {
            $notification->success();
        }

        $notification->send();
    }
}

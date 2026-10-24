<?php

namespace App\Filament\Resources\Students\Tables;

use App\Models\Student;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom berisi URL publik lengkap, jadi tidak perlu ->disk()
                ImageColumn::make('formal_photo_url')
                    ->label('Foto')
                    ->circular(),

                TextColumn::make('nim')
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nickname')
                    ->label('Panggilan')
                    ->searchable(),

                TextColumn::make('class_name')
                    ->label('Kelas')
                    ->badge()
                    ->sortable(),

                TextColumn::make('origin_city')
                    ->label('Kota Asal')
                    ->sortable(),

                TextColumn::make('whatsapp_no')
                    ->label('No. WhatsApp'),
            ])
            ->filters([
                SelectFilter::make('class_name')
                    ->label('Kelas')
                    ->options(Student::CLASSES),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nim');
    }
}
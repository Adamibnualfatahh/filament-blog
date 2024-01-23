<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExportResource\Pages;
use App\Filament\Resources\ExportResource\RelationManagers;
use App\Models\Export;
use Filament\Actions\CreateAction;
use Filament\Actions\Exports\Downloaders\CsvDownloader;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Env;
use League\Flysystem\Config;

class ExportResource extends Resource
{
    protected static ?string $model = Export::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('file_disk')
                    ->label('File Disk')
                    ->toggleable(),
                TextColumn::make('file_name')
                    ->label('File Name'),
                TextColumn::make('exporter')
                    ->label('Exporter')
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('User'),
                TextColumn::make('expoerter')
                    ->label('Download')
                    ->url(fn (Export $export) => Env::get('APP_URL') . '/storage/filament_exports/' . $export->id . '/' . $export->file_name . '.xlsx')
                    ->placeholder('Download')
                    ->openUrlInNewTab()
            ])
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExports::route('/'),
            'create' => Pages\CreateExport::route('/create'),
            'edit' => Pages\EditExport::route('/{record}/edit'),
        ];
    }
}

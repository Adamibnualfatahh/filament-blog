<?php

namespace App\Filament\Resources\PostResource\RelationManagers;

use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CommentRelationManager extends RelationManager
{
    protected static string $relationship = 'Comments';

    public function form(Form $form): Form
    {
        return $form
           ->schema([
            RichEditor::make('content')
                ->autofocus()
                ->required()
                ->name(__('Content')),
            Select::make('is_published')
                ->placeholder(__('Published'))
                ->options([
                    0 => 'No',
                    1 => 'Yes',
                ])
                ->default(1)
                ->required(),
            Hidden::make('user_id')
                ->default(Auth::user()->id),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                TextColumn::make('index')
                    ->rowIndex()
                    ->label(__('No.')),
                TextColumn::make('post.title')
                    ->label(__('Post'))
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(__('User'))
                    ->searchable(),
                TextColumn::make('content')
                    ->label(__('Content'))
                    ->searchable()
                    ->limit(50),
                IconColumn::make('is_published')
                    ->label(__('Published'))
                    ->boolean()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

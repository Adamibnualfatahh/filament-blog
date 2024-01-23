<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->autofocus()
                    ->required()
                    ->placeholder(__('Title'))
                    ->live(debounce: 250)
                    ->debounce(250)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->required()
                    ->placeholder(__('Slug'))
                    ->readOnly(),
                Select::make('category_id')
                    ->required()
                    ->placeholder(__('Category'))
                    ->options(
                        Category::all()->pluck('name', 'id')
                    ),
                 Select::make('is_published')
                    ->placeholder(__('Published'))
                    ->options([
                        0 => 'No',
                        1 => 'Yes',
                    ])
                    ->default(1)
                    ->required(),
                FileUpload::make('thumbnail')
                    ->required()
                    ->placeholder(__('Thumbnail'))
                    ->image()
                    ->directory('posts'),
                RichEditor::make('content')
                    ->required()
                    ->placeholder(__('Content')),
                Hidden::make('user_id')
                    ->default(Auth::user()->id),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->rowIndex()
                    ->label(__('No.')),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable()
                    ->description(fn(Post $post) => $post->content ? Str::limit($post->content, 50) : null),
                TextColumn::make('user.name')
                    ->sortable()
                    ->searchable()
                    ->expandableLimitedList(),
                TextColumn::make('category.name')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_published')
                    ->label(__('Published'))
                    ->boolean()
                    ->sortable(),
                ImageColumn::make('thumbnail')
                    ->square(),
                TextColumn::make('comments_count')
                    ->counts(['comments'])
                    ->sortable()
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\ViewAction::make()
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
            RelationManagers\CommentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}

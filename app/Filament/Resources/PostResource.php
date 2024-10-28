<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Set;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationGroup = "Blog";

    protected static ?string $pluralModelLabel = "Blog";
    protected static ?string $modelLabel = "notícia";

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Tabs::make('Criar novo post')->tabs([

                    Tab::make('Image data')
                        ->icon('heroicon-m-inbox')
                        ->badge('Teste')
                        ->schema([
                            TagsInput::make('tags')
                                ->required()
                                ->suggestions([
                                    'tailwindcss',
                                    'alpinejs',
                                    'laravel',
                                    'livewire',
                                ]),
                            Toggle::make('published')
                                ->required(),
                            FileUpload::make('thumbnail')
                                ->disk('public')
                                ->directory('thumbnails')->columnSpanFull(),
                    ]),

                    Tab::make('Conteudo')
                        ->icon('heroicon-m-inbox')
                        ->schema([
                        RichEditor::make('content')
                            ->columnSpanFull()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])

                ])->columnSpanFull()->activeTab(1)->persistTabInQueryString(),

                Section::make('Dados básicos da postagem')
                    ->description('Criação de postagem')
                    ->collapsible()
                    ->schema([

                        Group::make()->schema([
                            TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->live()
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            ColorPicker::make('color')
                                ->required(),
                        ]),

                        Select::make('category_id')
                            ->label('Categoria')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                            TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),



                            Select::make('authors casa')
                                ->label('Autores')
                                ->multiple()
                                ->preload()
                                ->relationship('authors', 'name'),

                            CheckboxList::make('authors casa')
                                ->label('Autores')
                                ->searchable()
                                ->relationship('authors', 'name'),


                ])->columnSpan(1)->columns(2)->columnSpanFull(),

            ])->columns([
                'default'   => 1,
                'md'        => 2,
                'lg'        => 2,
                'xl'        => 2,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('No')->state(
                    static function (HasTable $livewire, \stdClass $rowLoop): string {
                        return (string) (
                            $rowLoop->iteration +
                            ($livewire->getTableRecordsPerPage() * (
                                    $livewire->getTablePage() - 1
                                ))
                        );
                    }
                ),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Categoria')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->searchable(),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                ColorColumn::make('color')
                    ->searchable(),
                TextColumn::make('tags')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                IconColumn::make('published')
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('Posts ativos')->query(
                    function (Builder $query): Builder {
                        return $query->where('published', true);
                    }
                ),
                Tables\Filters\TernaryFilter::make('published')->label('Filtro por publicados ou não'),
                Tables\Filters\SelectFilter::make('category_id')->label('Categorias')
                    ->relationship('category', 'name')->preload()
                    ->multiple()

            ])
            ->actions([
//                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\AuthorsRelationManager::class,
            RelationManagers\CommentsRelationManager::class
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

    public static function getLabel(): ?string
    {
        $locale = app()->getLocale();

        if($locale == 'pt_BR'){
            return 'Notícia';
        }else{
            return 'Postagem';
        }
    }
}

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
	<a href="#"  target="_blank" title="stack">
		<img src="public/images/tall.JPG" alt="tall filament" style="border-radius: 5px;" width="400">
	</a>
</p>

<p align="center">
	<img src="https://img.shields.io/badge/version project-1.0-brightgreen" alt="version project filament">
    <img src="https://img.shields.io/badge/Php-8.2-informational&color=brightgreen" alt="stack project">
    <img src="https://img.shields.io/static/v1?label=Laravel&message=10.10&color=brightgreen?style=for-the-badge" alt="stack project">
    <img src="https://img.shields.io/static/v1?label=Livewire&message=3.0.1&color=brightgreen?style=for-the-badge" alt="stack project">
	<a href="https://opensource.org/licenses/GPL-3.0">
		<img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="GPLv3 License">
	</a>
</p>

# 🚀 Demonstrando Filament 3 Tutorial - `Básico`

> Vamos conhecer o Filament, um conjunto de modulos com muitos componentes que irão acelerar o desenvolvimento de nossas aplicações web.
>O Filament se baseia na `Tall` Stack (`TailWindCSS`, `AlpineJS`, `Laravel`, `Livewire`) e neste projeto vamos desenvolver um projeto 
>de clinica de exemplo da documentação e adicionar novas funcionalidades.


- [Site Filament laravel](https://filamentphp.com/).
- [Get started Filament](https://filamentphp.com/docs).
- [Panel Builder Installation](https://filamentphp.com/docs/3.x/panels/installation).

> :star: Este sistema de exemplo irá abordar o primeiro exemplo que a próprio documentação do `Filament` demosntra e 
>também irei adicionar novas funcionalidades para melhor aprendizado e melhorar o sistema.
> Obs.: Toda codificação e exeplicações do exemplo que a documentação apresenta, não irei descrever.

#### :speech_balloon: Descrição dos projetos `exemplo`
>`Exemplo | Filament`: :star2: Sera a construção de um sistema simples de clínica veterinária e com novas funcionalidades 
>descritas abaixo, terá gerenciamento de pacientes para uma clínica veterinária usando o Filament. 
>Apoiará a adição de novos `pacientes` (gatos, cães ou coelhos), atribuindo-os a um `proprietário` e registrando quais 
>`tratamentos` eles receberam. O sistema terá um painel com estatísticas sobre os tipos de pacientes e um gráfico com a 
>quantidade de tratamentos administrados no último ano.

<p align="center">
	<a href="#"  target="_blank" title="Diagrama">
		<img src="public/images/diagram-filament.jpg" alt="Diagram filament" style="border-radius: 5px;" width="600">
	</a>
</p>

#### :speech_balloon: Diagrama novo do projeto
> :star2: Continuando a construção do sistema da clínica, vamos adicionar novas funcionalidades, com um blog, que terá 
>uma lista de `posts` (notícias) com seus devidos autores, onde teremos associações de `categorias`, `comentários` e replicas de comentários dos `usuários`.
> E por fim também terá a adição lista de `produtos` que a clínica terá em seu estoque com associação a categorias.

<p align="center">
	<a href="#"  target="_blank" title="Diagrama">
		<img src="public/images/diagram.jpg" alt="Diagram filament" style="border-radius: 5px;" width="600">
	</a>
</p>

> :bell: No sistema da clínica, vamos poder verificar em cada view, suas associações conforme o Filament disponibiliza 
>com o gerenciamento das categorias, comentários, posts, pacientes, etc.

## :label: Config. database, migrate, models, etc.

#### :speech_balloon: Criando as migrates e models `inicias`

```
php artisan make:model Inventory -m
php artisan make:model Poost -m
php artisan make:model Category -m
```


#### :speech_balloon: Propriedades das `Migrations` [documentação laravel migrations table](https://laravel.com/docs/7.x/migrations)
> Vou demonstrar duas formas de relacionamento na migration, com os `exemplos` em Inventories e Post e suas associações com Categoria.

~~~~~~
    Schema::create('inventories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('description');
`        $table->string('image');
        $table->integer('quantity');
        $table->foreignIdFor(\App\Models\Category::class);
        $table->timestamps();
    });

    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->timestamps();
    });
~~~~~~

- :bell: Se não tem certeza com a chave, `category_id` ou qualquer outra chave, podemos usar a função `foreignIdFor` e 
passar a classe Eloquent, que automaticamente irá criar a coluna com o `nome da classe` e `_id`.

~~~~~~
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->string('thumbnail')->nullable();
        $table->string('title');
        $table->string('color');
        $table->string('slug')->unique();
        $table->foreignId('category_id')->constrained()->cascadeOnDelete();
        $table->text('content')->nullable();
        $table->json('tags')->nullable();
        $table->boolean('published')->default(false);
        $table->timestamps();
    });
~~~~~~


#### :speech_balloon: Adicionando nova coluna `active` no inventario

```
php artisan make:migration alter_inventory_table_add_active_column --table=inventories
```

~~~~~~
    Schema::table('inventories', function (Blueprint $table) {
        $table->boolean('active')->default(true);
    });
~~~~~~

#### :speech_balloon: Relacionamento das models. 
Estes são os metodos de relacionamento que iremos utilizar na relação `HasMany (1-1 & 1-M)`

~~~~~~
    //Inventory and Post
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    //Category
    public function products()
    {
        return $this->hasMany(Inventory::class);
    }
~~~~~~

#### :speech_balloon: Configurando o disco de armazenamento e o diretório
##### O disco por padrão é o publico, mas podemos modificar para outro e também definir um diretorio.
> Para que a imagem do produto apareça de forma correta, temos que ativa o `storage link` e modificar logo apos no arquivo `.ENV`
>a linha de `APP_URL` para receber a base do app `=http://127.0.0.1:8000`.

```
    php artisan storage:link
```

```
    FileUpload::make('thumbnail')
        ->disk('public')
        ->directory('thumbnails')->columnSpanFull(),
```

## :label: Filament 💥 

#### :speech_balloon: Comandos make:filament-
Vamos utilizar dois dos vários comandos que o Filament disponibiliza `filament-resource` e `filament-relation-manager`. 

- :boom: make:filament-relation-manager  :heavy_check_mark: O Filament permite que possamos gerenciar relacionamentos em nosso app. | [documentation](https://filamentphp.com/docs/3.x/panels/resources/relation-managers)
    - Os relacionamentos que podem ser gerenciados são `HasMany`, `HasManyThrough`, `BelongsToMany`, `MorphMany` e `MorphToMany`.
    > :heavy_check_mark: Os gerenciadores de relacionamento são tabelas interativas que permitem aos administradores listar, criar, anexar, associar, editar, desanexar, dissociar e excluir registros relacionados sem sair da página Editar ou Visualizar do recurso.
- :boom: make:filament-resource          :heavy_check_mark: Cria o arquivo de `resources` do seu modelo em App/Filament e cria toda estrutura das classes padrão.
    - Qualquer `model` que você criar em seu projeto laravel, podemos criar os Filaments em nosso projeto e ter páginas ou modais.

Criando as classes `views completas`| O `generate` irá add todas propriedades da sua migrate, criando páginas para seu projeto.

```
php artisan make:filament-resource Inventory --generate
php artisan make:filament-resource Post --generate
php artisan make:filament-resource User --generate
```

> Opção: Você pode criar de forma simples, views `simplificadas com MODAIs` no lugar de um página, como editar ou criar.

```
php artisan make:filament-resource Inventory --simple --generate
```

## :label: Relacionamento (1-1 & 1-M) `BelongsTo` e `HasMany`
Com os metodos de relacionamento criados nos models `BelongsTo` e `HasMany`, vamos add na view de `InventoryResource`, 
o relacionamento _*relationship*_ e ele tem dois argumentos.

> O primeiro argumento é o _nome do metodo_ no modelo e segundo a _proriedade_ que mostra.

~~~~~~
   Select::make('category_id')->relationship('category', 'name')
~~~~~~

#### :speech_balloon: Adicionar o Gerenciador de relacionamento
Para adiconar este gerenciador, utilizamos o comando abaixo e mais agluns argumentos como qual `resource` você quer gerenciar,
(Ex.: `CategoryResource`), segundo é o nome do relacionamento em sua model (Ex.: posts) e por último qual propriedade da model quer usar (title).

~~~~~~
php artisan make:filament-relation-manager CategoryResource posts title
~~~~~~

> O filament irá criar um outro diretorio em App/Filament/Resources/CategoryResource/RelaionManagers. chamado de PostsRelationManager.php.
> Esta mesma é o complemento da categoria, mostrando os relacionamentos que a categoria tem com seus posts, mas antes disso, como a propria documentação
>do Filament informa, precisamos dizer qual seu relacionament no metodo getRelations da CategoryResource.

~~~~~~
    public static function getRelations(): array
    {
        return [
            RelationManagers\PostsRelationManager::class
        ];
    }
~~~~~~


#### :bell: Validation | [documentation](https://filamentphp.com/docs/3.x/forms/validation)
Abaixo um exemplo dos `diversos metodos de validação dedicados` que o Filament inclui, mas você também pode usar 
quaisquer outras regras de validação do Laravel, incluindo regras de validação personalizadas.

~~~~~~
    TextInput::make('title')->required()
        ->alpha()
        ->doesntStartWith(['admin'])
        ->rules(['min:3|max:30', 'alpha'])
        ->in(['test', 'hello'])
~~~~~~

> Algo interessante que o Filament nos proporciona, é poder adiconar outras regras de validação proprias ou usar as validações
>que o proprio laravel disponibiliza | [documentation](https://laravel.com/docs/10.x/validation#available-validation-rules). 


## :label: Many-to-many relationships 
Nesta relação vamos ter uma `tabela pivo` que irá guardar os IDs de relação entre `User e Post`, assim vamos poder 
visualizar e gerenciar quais `autores` temos em cada `postagem`. E aqui vamos criar a relação, que terá como ser definida no
formulário de criação do post, mas também vamos criar o gerenciamento que o Filament permite criar.

#### :speech_balloon: Criação da tabela pivo como o `php artisan make:model post_user -m`.
~~~~~~
    Schema::create('post__users', function (Blueprint $table) {
        $table->id();
        $table->foreignIdFor(\App\Models\Post::class);
        $table->foreignIdFor(\App\Models\User::class);
        $table->timestamps();
    });
~~~~~~

#### :speech_balloon: Em User e Post criamos os `metodos` para relação.
~~~~~~
    //USER
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post__users')->withTimestamps();
    }

    //POST
    public function authors()
    {
        return $this->belongsToMany(User::class, 'post__users')->withTimestamps();
    }
~~~~~~

:speech_balloon: Em `PostResource` teremos `duas formas` de mostrar, multiple com `multiplos autores` (Array) e `CheckboxList autores`.

~~~~~~
        Select::make('authors casa')
            ->label('Autores')
            ->multiple()
            ->preload()
            ->relationship('authors', 'name'),

        Forms\Components\CheckboxList::make('authors casa')
            ->label('Autores')
            ->searchable()
            ->relationship('authors', 'name'),
~~~~~~



#### :speech_balloon: Gerenciamento dos autores
Agora vamos ao `filament-relation-manager` onde vamos criar o gerenciamento dos autores dos post, onde 
vamos poder adicionar novos autores ou vincular autores já cadastrados.

~~~~~~
    php artisan make:filament-relation-manager PostResource authors name
~~~~~~

#### :speech_balloon: Adicionando o RelationMangers de `AuthorRelationManger` na class PostResource.

~~~~~~
    public static function getRelations(): array
    {
        return [
            RelationManagers\AuthorsRelationManager::class
        ];
    }
~~~~~~

## :label: Tabela Pivot

#### :speech_balloon: Ajustando os metodos de relação de Post e User add `->withPivot('nota')`

> Coluna adicionada na migrate pivo post
~~~~~~
    $table->integer('nota')->default(0);
~~~~~~

- php artisan migrate:refresh --step=1

~~~~~~
    //Post e User add.
    public function authors()
    {
        return $this->belongsToMany(User::class, 'post__users')->withPivot('nota')->withTimestamps();
    }
~~~~~~

#### :speech_balloon: Add em `AuthorRelationManger` o vinculo de autores ao posta e já definindo a nota.
~~~~~~
->headerActions([
    Tables\Actions\AttachAction::make()
        ->form(fn (AttachAction $action): array => [
        $action->getRecordSelect(),
        Forms\Components\TextInput::make('nota')->required(),
    ]),
])
~~~~~~

## :label: Relações polimórficas (1-1 e 1-M)
#### :speech_balloon: No `relacionamentos polimórficos` vamos lidar com o relacionamento de `1 para 1` e `1 para muitos` e o `gerenciador` de relacionamento. 

> Alguns links de estudo que utilizei para aprendizado e desenvolvimento.
>   - [Docs relacionamentos do laravel](https://laravel.com/docs/10.x/eloquent-relationships)
>   - [Dev.to](https://dev.to/ellis22/learn-laravel-polymorphic-relationship-step-by-step-with-example-3pe3)
>   - [itsolutionstuff 1:1](https://www.itsolutionstuff.com/post/laravel-one-to-many-polymorphic-relationship-tutorialexample.html)
>   - [itsolutionstuff m:m](https://www.itsolutionstuff.com/post/laravel-many-to-many-polymorphic-relationship-tutorialexample.html)

~~~~~~
    //Add migrate
    php artisan make:model Comment -m
~~~~~~

~~~~~~
    //Property in migrate comments
    Schema::create('comments', function (Blueprint $table) {
        $table->id();
        $table->foreignIdFor(\App\Models\User::class);
        $table->morphs('comentable');
        $table->string('comment');
        $table->timestamps();
    });
~~~~~~

#### :speech_balloon: Gerenciamento dos comentários
~~~~~~
    php artisan make:filament-resource Comment
~~~~~~

#### :bell: Aqui, criaremos um modelo de tabela de `Comment, User e Post`. também usaremos "morphMany()" e "morphTo()" para relacionamento de ambos os modelos.

~~~~~~
    //Comment
    public $guarded = ['id'];
    
    /**
    * Comment
    * Obtenha todos os modelos comentáveis.
    */
    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
    * User e Post
    * Obtenha todos os comentários.
    */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
~~~~~~

#### :speech_balloon: CommentResource
:speech_balloon: No form passamos o relationship, comment e o MorphToSelect. 
:speech_balloon: `MorphToSelect`: Passamos em make o nome definido na migrate comment `$table->morphs('commentable')`.
:speech_balloon: `types`: Definimos um array, com quais recursos ou modelos podemos usar no relacionamento.

> No types teremos comentários em Postagens, usuários e em outros comentários.

~~~~~~
    //FORM
    Select::make('user_id')->relationship('user', 'name')->searchable()->preload(),
    TextInput::make('comment'),
    MorphToSelect::make('commentable')
        ->label('Tipo de comentários')
        ->types([
            Type::make(Post::class)->titleAttribute('title'),
            Type::make(User::class)->titleAttribute('email'),
            Type::make(Comment::class)->titleAttribute('id'),
        ])
        ->searchable()->preload()

    //TABLE
    ->columns([
        Tables\Columns\TextColumn::make('commentable_id'),
        Tables\Columns\TextColumn::make('commentable_type'),
        Tables\Columns\TextColumn::make('comment'),
    ])
~~~~~~

<p align="center">
	<a href="#"  target="_blank" title="Diagrama">
		<img src="public/images/comment.gif" alt="Diagram filament" style="border-radius: 5px;" width="100%">
	</a>
</p>

#### :speech_balloon: Gerenciando os comentários por postagem
:speech_balloon: Depois de `filament-relation-manager`, primeiro argumento a classe que terá o gerenciador `Post` e 
segundo o `nome do metodo` que faz o relacionamento `mophMany` e último é o identificador dos comentários `commentable`.
~~~~~~
    php artisan make:filament-relation-manager Post comments commentable
~~~~~~

#### :speech_balloon: Definindo relationship
:speech_balloon: Depois, basta definir o gerenciamento a relação em `getRelations` do `PostResource` e `CommentResource`.

## :label: Recursos adicionais - opcionais
:speech_balloon: Aqui `alguns recursos` do Filament que achei `interessante` demonstrar, mas você pode verificar melhor na
documentação do Filament.

#### :speech_balloon: Layouts ( Section & Group, Tabs) 
Alguns detalhes/Dicas de `GRIDs` `Groups`, `Sections` com columns e columnSpans.

~~~~~~
    
    return $form->schema([
        RichEditor::make('content')->columnSpan(3) //ou 'full' ou ->columnSpanFull()
    ])->columns(3),

    Forms\Components\Grid::make()->schema([
        //...
    ])->columns(2),
    
    //Forms
    return $form
            ->schema([
                Section::make('Dados básicos da postagem')
                    ->description('Criação de postagem')
                    ->collapsible()
                    ->schema([
                    //..
                ])->columnSpan(1)->columns(2),

                Section::make('description')
                    ->schema([
                    //...
                ])->columnSpan(1)->columns(2),
    ])->columns([
          'default'   => 1,
          'md'        => 2,
          'lg'        => 2,
          'xl'        => 2,
      ]);
~~~~~~

> Com o `collapsible()` podemos fazer com que uma seção seja recolhida, usando o collapsed atributo. O `make("...")` e `description("...")`
>são titulo e subtitulo e `aside()` se adicionado, podemos alinha a div a esquerda.

<p align="center">
	<a href="#"  target="_blank" title="Diagrama">
		<img src="public/images/layouts.jpg" alt="layouts" style="border-radius: 5px;" width="600">
	</a>
</p>

#### :speech_balloon: Tabs
As Guias ou `"Tabs"`, ajuda muito no front, por oferecer uma exibição de diversas telas em uma única guia.

~~~~~~
    Forms\Components\Tabs::make('Criar novo post')->tabs([
        Forms\Components\Tabs\Tab::make('Image data')->icon('heroicon-m-inbox')->schema([
            //...
        ]),
    
        Forms\Components\Tabs\Tab::make('Conteudo')->icon('heroicon-m-inbox')->schema([
            //...
        ])
    ])->columnSpanFull()->activeTab(1)->persistTabInQueryString(),
~~~~~~

<p align="center">
	<a href="#"  target="_blank" title="Diagrama">
		<img src="public/images/tabs.gif" alt="layouts" style="border-radius: 5px;" width="100%">
	</a>
</p>

#### :speech_balloon: Tab in Tables
:speech_balloon: [Link docs.](https://filamentphp.com/docs/3.x/panels/resources/listing-records) Guias em tabelas de listas personalizadas. Ex. Em `Filament\Resources\PostResource\Pages\ListPosts`

~~~~~~
    public function getTabs(): array
    {
        return [
            'Todos'     => Tab::make(),
            'Ativos'    => Tab::make()->modifyQueryUsing(function (Builder $query){
                $query->where('published', true);
            }),
            'inativos'  => Tab::make()->modifyQueryUsing(function (Builder $query){
                $query->where('published', false);
            })
        ];
    }
~~~~~~

<p align="center">
	<a href="#"  target="_blank" title="Diagrama">
		<img src="public/images/tabTable.jpg" alt="Diagram filament" style="border-radius: 5px;" width="90%">
	</a>
</p>

#### :speech_balloon: Filtros
Na tabela podemos adicionar filtros para todos tipos de propriedades que temos em nosso projeto e aqui vai dois exemplos.
> `Filter` aborda a propriedade booleana para ativos e não e a `TernaryFilter` aborda da mesma forma, mas simplificada. 
>E a `SelectFilter` temos o filtro por categoria utilizando o relacionamento.

~~~~~~
    ->filters([
        Filter::make('Posts ativos')->query(
            function (Builder $query): Builder {
                return $query->where('published', true);
            }
        ),
        TernaryFilter::make('published')->label('Filtro por publicados ou não')->default(true),
        SelectFilter::make('category_id')->label('Categorias')
            ->relationship('category', 'name')->preload()
            ->multiple()

    ])
~~~~~~

<p align="center">
	<a href="#"  target="_blank" title="Diagrama">
		<img src="public/images/filters.gif" alt="layouts" style="border-radius: 5px;" width="100%">
	</a>
</p>

~~~~~~
php artisan make:filament-widget PatientTypeOverview --stats-overview
php artisan make:filament-widget TreatmentsChart --chart

composer require flowframe/laravel-trend
~~~~~~

#### :speech_balloon: Authorization | [Policy Filament.](https://filamentphp.com/docs/3.x/panels/resources/getting-started#authorization)
:speech_balloon: De acordo com a [documentação Laravel](https://laravel.com/docs/10.x/authorization#creating-policies) (10.x), Políticas são classes que organizam a lógica de autorização 
em torno de um modelo ou recurso específico. E o `Filament se utiliza de todas Polices criada juntamente com os metodos padrões criados no comando abaixo e com outros metodos`.

:speech_balloon: Cria a policy de Post e os metodos (--model=Category) exemplo de `CRUD da policy`.

~~~~~~
    php artisan make:policy CategoryPolicy --model=Category
~~~~~~

##### :speech_balloon: Ignorando autorização
:speech_balloon: Se quiser `ignorar a autorização` de um recurso, você pode definir a `$shouldSkipAuthorizationpropriedade` como `true`:

~~~~~~
    protected static bool $shouldSkipAuthorization = true;
~~~~~~

### :star: Contatos

Contatos 👇🏼 [rafaelblum_digital@hotmail.com]

[![Youtube Badge](https://img.shields.io/badge/-Youtube-FF0000?style=flat-square&labelColor=FF0000&logo=youtube&logoColor=white&link=https://www.youtube.com/channel/UCMvtn8HZ12Ud-sdkY5KzTog)](https://www.youtube.com/channel/UCMvtn8HZ12Ud-sdkY5KzTog)
[![Instagram Badge](https://img.shields.io/badge/-rafablum_-violet?style=flat-square&logo=Instagram&logoColor=white&link=https://www.instagram.com/rafablum_/)](https://www.instagram.com/rafablum_/)
[![Twitter: universoCode](https://img.shields.io/twitter/follow/universoCode?style=social)](https://twitter.com/universoCode)
[![Linkedin: RafaelBlum](https://img.shields.io/badge/-RafaelBlum-blue?style=flat-square&logo=Linkedin&logoColor=white&link=https://www.linkedin.com/in/rafael-blum-378656285/)](https://www.linkedin.com/in/rafael-blum-378656285/)
[![GitHub RafaelBlum](https://img.shields.io/github/followers/RafaelBlum?label=follow&style=social)](https://github.com/RafaelBlum)

<br/>

<img src="https://media.giphy.com/media/LnQjpWaON8nhr21vNW/giphy.gif" width="60"> <em><b>Adoro me conectar com pessoas diferentes,</b> então se você quiser dizer <b>oi, ficarei feliz em conhecê-lo mais!</b> :)</em>

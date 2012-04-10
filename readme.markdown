Introduction
------------

### What

Model is a simple, lightweight and easy-to-use Domain Driven Entity framework.

### Why

Because you want your models to be defined by your business requirements not database requirements. You also want control over how backends are used to access data whether it be Zend, Doctrine, Propel or simply just PDO or MongoDB. You can even call an external service or read data from an XML file.

Theory of Abstraction
---------------------

Since you are not tied to a specific backend, you are free to choose how you structure your entities and repositories without thinking about how it will be stored and how it will be retrieved. When you structure your entities, you should think solely about how you will be using them from a domain perspective not how it will be stored in the backend. Mappers can be used to import and export between backends and business entities.

Entities
------------------

To create an entity, all you really have to do is extend the base entity class:

    <?php
    
    namespace Model\Entity;
    
    class Content extends Entity
    {
        
    }

### Value Objects

Value objects are used to define properties. By default, a range of value objects are provided under the `Model\Vo` namespace:

* `Alias` - Allows one property to act as another property.
* `Boolean` - Value is cast as a `boolean`.
* `Date` - Value is passed into and manipulated using a `DateTime` object.
* `Filter` - Allows a value to be filtered before it is set.
* `Float` - Value is cast as a `float`.
* `Generic` - A basic VO that passes through any value.
* `HasMany` - Allows a one-to-many relationship to a given entity set of other entities.
* `HasOne` - Allows a one-to-one relationship to another entity.
* `Integer` - Value is cast as an `int`.
* `Money` - Value is cast as a `string` and `number_format()` is used to format the string to 2 decimal places.
* `Proxy` - Takes a proxy callback to use for retrieving the value if it has not been set. Once loaded, that value is reused for the lifetime of the object.
* `Set` - Represents a set, or array, of arbitrary values.
* `String` - Value is cast as a `string`.

### Mappers

Mappers are used to translate information going into or out of your entities much like DTO's. For example, your content entity may have many fields, but your fields are sub-objects of your content entity. When you go to store this information, you need to somehow format the information so that it can be easily inserted into a database.

    <?php
    
    namespace Model\Mapper;
    use DateTime;
    
    class ContentToDb extends Mapper
    {
        public $move = [
            'id'          => 'content.id',
            'title'       => 'content.title',
            'description' => 'content.description',
            'created'     => 'content.created',
            'updated'     => 'content.updated',
            'id'          => 'fields.$.id'
        ];
        
        public content__created($date)
        {
            return $this->toMysqlDateTime($date);
        }
        
        public content__updated($date)
        {
            return $this->toMysqlDateTime($date);
        }
        
        private function toMysqlDateTime(DateTime $date)
        {
            return $date->format('Y-m-d H:i:s');
        }
    }

### Configuration

By default, entities are configured using doc comment tags applied to the class itself or its properties.

Supported `class` doc tags:

* `@mapper` - Applies a mapper to the entity.

Supported `property` doc tags:

* `@vo` - Applies a value object to the entity.

If you don't like this approach and want to configure your entity programatically, you can use the `configure()` hook and use the built-in methods:

    public function configure()
    {
        $this->setMapper('mymapper', new Model\Mapper\MyMapper);
        $this->setVo('id', new Model\Vo\Integer);
    }

To apply a mapping to a class you use the `@mapper` doc tag:

    /**
     * Main content item.
     * 
     * @mapper fromDb Model\Mapper\Content\FromDb
     * @mapper toDb   Model\Mapper\Content\ToDb
     * @mapper toApi  Model\Mapper\Content\ToApi
     */
    class Content extends Entity
    {
    
    }

### Relationships

Relationships are defined using the `HasOne` and `HasMany` value objects:

    <?php

    namespace Model\Entity;

    class Content extends Entity
    {
        
    }

By adding relationships, you ensure that if the specified property is set or accessed, that it is an instance of the specified class.

    <?php
    
    use Entity\Content;
    
    $entity = new Content;
    
    // instance of \Entity\Content\User
    $user = $entity->user;
    
    // instance of \Model\EntitySet containing instances of \Entity\Content\Modification
    $modifications = $entity->modifications;

This means that if you set an array to one of these properties, it will ensure that an instance of the specified relationship is instantiated and filled with the specified array data.

    $entity->user = array('name' => 'Me');

And you can even pass any traversable item:

    $user       = new stdClass;
    $user->name = 'Me';
    
    // applying a stdClass
    $entity->user = $user;
    
    // entity sets work the same way
    $entity->modifications = array(
        array('name' => 'Me'),
        new stdClass,
    );

Autoloading Data: Proxies
-------------------------

Instead of always having to manually load external data or relationships onto an entity, you can specify a proxy callback to load the data for you.

    namespace Entity;
    use Repository\Content as ContentRepository;
    use Repository\User as UserRepository;

    class Content
    {
        public function init()
        {
            // set up the proxy
            $this->proxy('user', function(Content $content) {
                $repo = new UserRepository;
                return $repo->findById($content->idUser);
            });

            // and if you are loading a relation you can ensure an entity is created
            $this->hasOne('user', '\Entity\Content\User');

            // you can even load arbitrary data
            $this->proxy('views', function(Content $content) {
                $repo = new ContentRepository;
                return $repo->getNumberOfViews($content->id);
            });
        }
    }

Now when we get a content item, we can autoload the user:

    use Repository\Content as ContentRepository;

    $content = new ContentRepository;
    $content = $content->getById(1);

    isset($content->user); // false
    $content->user->id; // 1 (or some other value)

Or you can just manage local data:

    namespace Entity;

    class User
    {
        public function init()
        {
            $this->proxy('name', function(User $user) {
                return $user->firstName . ' ' . $user->lastName;
            });
        }
    }

Good things about proxies:
* Autoloading means if you don't use the data, then you won't load the data.
* You can manage your own caching which means you may not have to make that extra query.
* If you load using a repository method, then you can just use that to manage the cache.
* Using a closure allows for greater flexibility if necessary.

Of course, if you are neurotic about running more than one query for data you can always just load it all at once and map it to the object from your query result or however you want to do it in your repository.

Authoring Repositories
----------------------

Authoring repositories is fairly straight forward:

    <?php
    
    namespace Repository;
    use Model\Repository;
    
    class Content extends Repository
    {
        
    }

You are free to define your own base class for abstracted functionality and your own method definitions. By extending the base `\Model\Repository`, you have access to caching methods which make caching easier than managing your own drivers. However, if you use MongoDB, you may not have to cache at all.

Easing the Mapping of Data
--------------------------

When given the open-ended structure of defining your own storage implementations, you may be asking how in the heck you would separate data from an entity. To mitigate this, a mapper is included to map your data any way you want.

    <?php
    
    use Entity\Content;
    use Model\Mapper;
    
    // set up the entity
    $content = new Content;
    $content->hasOne('user', '\Entity\Content\User');
    
    // set the content data
    $content->title   = 'My Blerg Prost';
    $content->created = '2011-03-29 20:00:00';
    $content->updated = '2011-03-29 20:00:00';
    
    // and the user data
    $content->user->id   = 1;
    $content->user->name = 'Me Meeson';
    
    // split the data up
    $mapper = new Mapper;
    $mapper->map('title', 'content.title');
    $mapper->map('created', 'content.created');
    $mapper->map('updated', 'content.updated');
    $mapper->map('user.id', array('content.idUser', 'user.id');
    $mapper->map('user.name' array('content.author', 'user.name');

Now, calling:

    $mapper->convert($content->export());

Would return:

    array(
        'content' => array(
            'title'   => 'My Blerg Prost',
            'created' => '2011-03-29 20:00:00',
            'updated' => '2011-03-29 20:00:00',
            'idUser'  => 1,
            'author'  => 'Me Meeson'
        ),
        'user' => array(
            'id'   => 1,
            'name' => 'Me Meeson'
        )
    )

The mapper has segregated user information and content information as well as mapped the required user data into the content array. You can now use the mapped data to save each set of information off to their respective places however you intend to.

You probably wouldn't want to manually specify your mapping in your repositories, though, for the sake of maintainability. The mapper allows you to create a sub-class of it and specify an `init` method to set up your mapping definition:

    <?php
    
    namespace Map;
    use Model\Mapper;
    
    class Content extends Mapper
    {
        public function init()
        {
            $this->map('title', 'content.title');
            $this->map('created', 'content.created');
            $this->map('updated', 'content.updated');
            $this->map('user.id', array('content.idUser', 'user.id');
            $this->map('user.name' array('content.author', 'user.name');
        }
    }

This way you can map your data by just using an instance of the `\Map\Content` class:

    <?php
    
    use Entity\Content as ContentEntity;
    use Map\Content as ContentMap;
    
    $content = new ContentEntity;
    
    // ...
    
    $mapper = new ContentMap();
    $mapped = $mapper->convert($content->export());

You can also pass more than one array to `convert()`:

    $mapped = $mapper->convert($content->export(), array('title' => 'My Overridden Title'));

Array's are merged as if using `array_merge()` and then converted.
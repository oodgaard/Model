Introduction
------------

### What

Model is a simple, lightweight and easy-to-use Domain Driven Entity framework.

### Why

Because you want your models to be defined by your business requirements not database requirements. You also want control over how backends are used to access data whether it be Zend, Doctrine, Propel or simply just PDO or MongoDB. You can even call an external service or read data from an XML file.

Theory of Abstraction
---------------------

Because you are not tied to a specific backend, you are free to choose how you structure your entities and repositories without being funnelled into a specific way of doing it such as frameworks like Doctrine impose on you. I am not saying Doctrine is bad, but even with Doctrine 2, they still haven't fully forgotten the Active Record design pattern which completely negates a domain driven approach which it is attempting to facilitate.

Authoring Entities
------------------

To create an entity, all you really have to do is extend the base entity class:

    <?php
    
    namespace Entity;
    use Model\Entity;
    
    class Content extends Entity
    {
        
    }

### Relationships

You can also map relationships to other entities:

    <?php

    namespace Entity;
    use Model\Entity;

    class Content extends Entity
    {
        public function init()
        {
            $this->hasOne('user', '\Entity\Content\User');
            $this->hasMany('modifications', '\Entity\Content\Modification');
        }
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
- Autoloading means if you don't use the data, then you won't load the data.
- You can manage your own caching which means you may not have to make that extra query.
- If you load using a repository method, then you can just use that to manage the cache.
- Using a closure allows for greater flexibility if necessary.

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
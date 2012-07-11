Introduction
------------

### What

Model is a simple, lightweight and easy-to-use Domain Driven Entity framework written using PHP 5.4.x in mind.

### Why

Because you want your models to be defined by your business requirements not database requirements. You also want control over how backends are used to access data whether it be Zend, Doctrine, Propel or simply just PDO or MongoDB. It even leaves you free to use other data sources and libraries; there's really no strings attached.

Theory of Abstraction
---------------------

Since you are not tied to a specific backend, you are free to choose how you structure your entities and repositories without basing it on how it will be stored or retrieved. When you structure your entities, you should think solely about how you will be using them from a domain perspective not how it will be stored in the backend. Mappers can be used to import and export between backends and business entities.

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
    
    namespace Model\Mapper\Content;
    use DateTime;
    
    class ToDb extends Mapper
    {
        public $move = [
            'id'          => 'content.id',
            'title'       => 'content.title',
            'description' => 'content.description',
            'created'     => 'content.created',
            'updated'     => 'content.updated',
            'id'          => 'fields.$.id'
        ];
        
        public $filters = [
            'content.created' => 'filterDate',
            'content.updated' => 'filterDate'
        ];
        
        public function filterDate($date)
        {
            return date('Y-m-d H:i:s', strtotime($date));
        }
    }

### Validators

Validators are used to validate the state of an entity. The only requirement for a validator is that it `is_callable()`. There are two types of ways to validate your entities. You can either attach a validator to the entity itself or directly to a value object.

Attaching them to your entities are handy when you need to validate the entity's state based on different property values. You may have one value that depends on another.

    $entity->addValidator('That's malarkey! The content ":title" cannot be created before it is updated.', function($entity) {
        return $entity->created <= $entity->updated;
    });

Attaching them to your value objects are great for when you are doing very specific validation on a type of value like whether or not the created date is a valid date.

    $entity->getVo('title')->addValidator('":title" is an invalid content title.', function($vo) {
        return (new Zend\Validator\Alnum)->isValid($vo->get());
    });

As you can see, we used both a generic `<=` operator and a Zend Validator. If you decide to use the `@valid` on a value object described in the next section, it will allow you to use a pass a generic class name implementing `__invoke()` as well as a Zend Framework 1.x and 2.x validator class name.

*If you need to pass options to the validator, it is recommeded to use the `addValidator()` method instead of the `@valid` tag.*

### Configuration

By default, entities are configured using doc comment tags applied to the class itself or its properties.

Supported `class` doc tags:

* `@map` Applies a mapper to the entity.
* `@valid` Adds a validator to the entity. This can be any class implementing `__invoke()` or Zend Validator.

Supported `property` doc tags:

* `@auto` Sets an autoloader for the value object.
* `@valid` Adds a validator to the value object. Must be applied after `@vo`. Must also be `is_callable()` or a Zend 1.x or 2.x validator.
* `@vo` Applies a value object to the entity. Must be applied before `@valid`.

If you don't like this approach and want to configure your entity programatically, you can use the `configure()` hook and use the built-in methods:

    public function configure()
    {
        $this->setMapper('mymapper', new Model\Mapper\MyMapper);
        $this->setVo('id', new Model\Vo\Integer);
    }

Here is an example entity using the above tags:

    <?php
    
    namespace Model\Entity;
    use Model\Behavior\Timestampable;
    
    /**
     * Main content item.
     * 
     * @mapper fromDb Model\Mapper\Content\FromDb
     * @mapper toApi  Model\Mapper\Content\ToApi
     * @mapper toDb   Model\Mapper\Content\ToDb
     * 
     * @valid Model\Validator\Content
     */
    class Content extends Entity
    {
        use Timestampable;
        
        /**
         * The content id.
         * 
         * @vo Model\Vo\Integer
         * 
         * @valid Zend\Validator\Int
         */
        public $id;
        
        /**
         * The URL slug for the content item.
         * 
         * @auto autoloadSlug
         * 
         * @vo Modle\Vo\String
         * 
         * @valid Zend\Validator\Alnum The content :title's slug is invalid.
         */
        public $slug;
        
        /**
         * The content title.
         * 
         * @vo Model\Vo\String
         * 
         * @valid Zend\Validator\Alnum   The content ":title" title is invalid.
         * @valid Model\Validator\Unique The content ":title" title is already taken.
         */
        public $title;
        
        /**
         * The content body.
         * 
         * @vo Model\Vo\String
         * 
         * @valid Zend\Validator\NotEmpty The content :title must not be empty.
         */
        public $body;
        
        /**
         * Returns the slug based on the title.
         * 
         * @return string
         */
        public function autoloadSlug()
        {
            return preg_replace('/[^a-zA-Z0-9\-]+/', '-', $this->title;
        }
    }

### Behaviors via Traits

You'll notice the use of the `Timestampable` trait. This trait is not included in the library, however, it exemplifies how you can use traits to mix functionality into your entities. We assume the trait has the following definition:

    <?php
    
    namespace Model\Behavior;
    
    trait Timestampable
    {
        /**
         * When the item was created.
         * 
         * @vo Model\Vo\Datetime
         * 
         * @valid Zend\Validator\Date The created date ":created" is not valid.
         */
        public $created;
        
        /**
         * When the item was last updated.
         * 
         * @vo Model\Vo\Datetime
         * 
         * @valid Zend\Validator\Date The last updated date ":updated" is not valid.
         */
        public $updated;
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

### Validating an Entity

When it comes time to validate your entity, you have two options. First, you can simply validate the entity and get it's error messages.
    
    if ($errors = $entity->validate()) {
        // do some error handling
    }

However, when your root entity is not valid, you'll most likely want to halt execution and catch it somewhere. The `assert()` method allows you to do just that.

    // validate and throw an exception if it's not valid
    $entity->assert('Some errors occured.', 1000);

Asserting will throw a special type of exception which is an isntance of `Model\Validator\ValidatorException`. This exception class allows you to get each error message that was caught during the entire validation of the entity. This includes all entity validators and value object validators of the root entity and all child entities.

This allows you to catch that somewhere in your code.

    use Model\Validator\ValidatorException;
    use Exception;

    try {
        // do something like dispatch your application
    } catch (ValidatorException $e) {
        // handle validation errors
    } catch (Exception $e) {
        // fatal exception
    }

You can handle that any way you want using the methods in the exception:

    <?php
    
    use Model\Validator\ValidatorException;
    
    // allows a main message
    $exception = new ValidatorException('The following errors happened:');
    
    // allows you to add messages
    $exception->addMessage('my first message');
    $exception->addMessages([
        'my second message',
        'my third message'
    ]);
    
    // implements IteratorAggregate
    foreach ($exception as $message) {
        ...
    }
    
    // The following errors happened:
    // 
    // - my first message
    // - my second message
    // - my third message
    // 
    // [stack trace goes here]
    echo $exception;
    
    // or you can just throw it
    throw $exception;

Repositories
------------

Authoring repositories is fairly straight forward:

    <?php
    
    namespace Model\Repository;
    use Model\Entity;
    
    class Content implements RepositoryInterface
    {
        // enables caching methods
        use Cacheable;
        
        public function getById($id)
        {
            ...
        }
        
        public function getByTitle($title)
        {
            ...
        }
        
        public function save(Entity\Content $content)
        {
            ...
        }
    }

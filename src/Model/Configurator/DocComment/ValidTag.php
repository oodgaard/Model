<?php

namespace Model\Configurator\DocComment;
use InvalidArgumentException;
use Model\Entity\Entity;
use ReflectionClass;
use ReflectionProperty;
use Reflector;
use RuntimeException;
use Zend\Validator\Validator;
use Zend_Validate_Interface;

/**
 * Uses doc comments to configure an entity.
 * 
 * Adds a validator to the entity.
 * 
 * @category Configurators
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  http://europaphp.org/license
 */
class ValidTag implements DocTagInterface
{
    /**
     * The validator instance cache.
     * 
     * @var array
     */
    private $cache = [];
    
    /**
     * Configures the entity with the specified VO.
     * 
     * @param string    $value  The doc tag string minus the tag name.
     * @param Reflector $refl   The reflector object representing the item with the corresponding tag.
     * @param Entity    $entity The entity being configured.
     * 
     * @return void
     */
    public function configure($value, Reflector $refl, Entity $entity)
    {
        // parse out the tag parts
        $parts     = explode(' ', $value, 2);
        $validator = $this->resolveValidator($parts[0], $entity);
        $message   = isset($parts[1]) ? $parts[1] : null;
        
        // add to the entity or vo
        if ($refl instanceof ReflectionProperty) {
            $this->configureProperty($refl, $entity, $validator, $message ?: 'Vo "' . $refl->getName() . '" is not valid.');
        } else {
            $this->configureClass($entity, $validator, $message ?: 'Entity "' . get_class($entity) . '" is not valid.');
        }
    }
    
    /**
     * Configures a class.
     * 
     * @param Entity $entity    The entity to configure.
     * @param mixed  $validator The validator to add.
     * @param string $message   The message to give the validator.
     * 
     * @return void
     */
    private function configureClass(Entity $entity, $validator, $message)
    {
        $entity->addValidator($message, $validator);
    }
    
    /**
     * Configures a property.
     * 
     * @param ReflectionProperty $property  The property to configure.
     * @param Entity             $entity    The entity to configure.
     * @param mixed              $validator The validator to add.
     * @param string             $message   The message to give the validator.
     * 
     * @return void
     */
    private function configureProperty(ReflectionProperty $property, Entity $entity, $validator, $message)
    {
        // the property name dictates the VO name
        $property = $property->getName();
        
        // @vo must be specified before @valid
        if (!$entity->hasVo($property)) {
            throw new RuntimeException(sprintf(
                'You cannot apply the @valid tag to "%s::$%s" because it has not been given a VO yet.',
                get_class($entity),
                $property
            ));
        }
        
        // add the validator to the specified VO
        $entity->getVo($property)->addValidator($message, $validator);
    }

    private function resolveValidator($validator, $entity)
    {
        // if a validator has been cached, we assume we can reuse it
        if (isset($this->cache[$validator])) {
            $validator = $this->cache[$validator];
        } elseif (method_exists($entity, $validator)) {
            $validator = [$entity, $validator];
        } elseif (class_exists($validator)) {
            $validator = new $validator;

            if ($validator instanceof Zend_Validate_Interface || $validator instanceof Validator) {
                $validator = function($value) use ($validator) {
                    return $validator->isValid($value);
                };
            }

            // only cache class instances
            $this->cache[get_class($validator)] = $validator;
        } elseif (function_exists($validator)) {
            $this->cache[$validator] = $validator;
        } else {
            throw new RuntimeException(sprintf('Unknown validator "%s" specified for "%s".', $validator, get_class($entity)));
        }

        return $validator;
    }
}
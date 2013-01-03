<?php

namespace Test;
use Provider\ContentEntity;
use Provider\ContentRepository;
use Provider\UserEntity;
use Provider\UserRepository;
use Testes\Test\UnitAbstract;

class RepositoryTest extends UnitAbstract
{
    public function creating()
    {
        $entity = new ContentEntity([
            'name' => 'Dick Richard'
        ]);
        
        ContentRepository::create($entity);

        $entity = ContentRepository::findById($entity->id);
        
        $this->assert($entity instanceof ContentEntity, 'Entity instance not returned.');
    }
    
    public function updating()
    {
        $entity = new ContentEntity([
            'name' => 'original content'
        ]);
        
        ContentRepository::create($entity);
        
        $entity->name = 'updated content';
        
        ContentRepository::update($entity);

        $entity = ContentRepository::findById($entity->id);
        
        $this->assert($entity->name === 'updated content', 'The entity was not updated.');
    }
    
    public function removing()
    {
        $entity = new ContentEntity([
            'name' => 'test'
        ]);
        
        ContentRepository::create($entity);

        if (!ContentRepository::findById($entity->id)) {
            $this->assert(false, 'Cannot remove if item cannot be saved.');
        }
        
        ContentRepository::remove($entity);

        if (ContentRepository::findById(1)) {
            $this->assert(false, 'Item was not removed');
        }
    }
    
    public function caching()
    {
        $item = new ContentEntity;

        ContentRepository::initInstance();
        ContentRepository::create($item);
        
        $item = ContentRepository::findById($item->id);

        if (!$item) {
            $this->assert(false, 'Item should have been found.');
        }
        
        $item = ContentRepository::findById($item->id);
        $repo = ContentRepository::getInstance();

        $this->assert($repo->findByIdCallCount === 1, 'Method "ContentRepository->findById()" was called more than once so the cache did not find the item.');
    }

    public function settingUp()
    {
        $repo = ContentRepository::getInstance('test', [true]);
        $this->assert($repo->setUp, 'The repository was not set up.');
    }
}
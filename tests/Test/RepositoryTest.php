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
            'name' => 'Trey Shugart'
        ]);
        
        ContentRepository::create($entity);

        $entity = ContentRepository::findById($entity->id);
        
        $this->assert($entity instanceof ContentEntity, 'Entity instance not returned.');
    }
    
    public function updating()
    {
        $entity = new ContentEntity([
            'name' => 'my content'
        ]);
        
        ContentRepository::create($entity);
        
        $entity->wasSaved = true;
        
        ContentRepository::update($entity);

        $entity = ContentRepository::findById($entity->id);
        
        $this->assert($entity->name === 'my content', 'The entity was not updated.');
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

        ContentRepository::create($item);
        
        $item = ContentRepository::findById($item->id);

        if (!$item) {
            $this->assert(false, 'Item should have been found.');
        }
        
        $item = ContentRepository::findById($item->id);

        if ($repo->findByIdCallCount > 1) {
            $this->assert(false, 'Method "ContentRepository->findById()" was called more than once so the cache did not find the item.');
        }
    }

    public function repositoryInit()
    {
        $argument = 'test';
        MyTestRepository::init($argument);
        $this->assert(MyTestRepository::$argument == $argument, 'The argument was not initialised');
    }
}

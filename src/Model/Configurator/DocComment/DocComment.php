<?php

namespace Model\Configurator\DocComment;

class DocComment implements DocCommentInterface
{
    private $tags;

    public function __construct($comment)
    {
        $this->tags = new DocTagCollection;

        $definitions = preg_split('/\* @/', $comment);
        $this->formatDefinitions($definitions);
        
        foreach ($definitions as $definition) {
            $this->tags->add(new DocTag($definition));
        }
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function getIterator()
    {
        return $this->tags;
    }

    private function formatDefinitions(array &$definitions)
    {
        foreach ($definitions as &$definition) {
            $definition = trim($definition);
            $definition = trim($definition, '*/');
            $definition = trim($definition);
        }

        $definitions = array_filter($definitions);
    }
}
<?php

namespace Model\Configurator\DocComment;

class DocComment implements DocCommentInterface
{
    private $tags;

    private static $cache = [];

    public function __construct($comment)
    {
        $this->tags = new DocTagCollection;

        if (isset(self::$cache[$comment])) {
            $definitions = self::$cache[$comment];
        } else {
            $definitions = self::$cache[$comment] = $this->parseDefinitions($comment);
        }
        
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

    private function parseDefinitions($comment)
    {
        $definitions = preg_split('/\* @/', $comment);
        $this->formatDefinitions($definitions);
        return $definitions;
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
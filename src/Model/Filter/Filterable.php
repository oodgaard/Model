<?php

namespace Model\Filter;
use Model\Util\DotNotatedArray;

trait Filterable
{
    private $importFilters;

    private $exportFilters;

    public function addImportFilter($name, callable $filter)
    {
        $this->getImportFilters()->offsetSet($name, $filter);
        return $this;
    }

    public function addExportFilter($name, callable $filter)
    {
        $this->getExportFilters()->offsetSet($name, $filter);
        return $this;
    }

    public function getImportFilters()
    {
        if (!$this->importFilters) {
            $this->importFilters = new DotNotatedArray;
        }

        return $this->importFilters;
    }

    public function getExportFilters()
    {
        if (!$this->exportFilters) {
            $this->exportFilters = new DotNotatedArray;
        }
        
        return $this->exportFilters;
    }
}
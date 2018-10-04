<?php

namespace tests;

use creocoder\flysystem\Filesystem;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Memory\MemoryAdapter;

class MemoryFilesystem extends Filesystem
{
    /**
     * @return AdapterInterface
     */
    protected function prepareAdapter()
    {
        return new MemoryAdapter();
    }
}

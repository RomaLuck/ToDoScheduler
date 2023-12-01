<?php

namespace App\Service;

use App\Contracts\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

class FilesCacheService implements CacheInterface
{
    public TagAwareAdapter $cache;

    public function __construct()
    {
        $this->cache = new TagAwareAdapter(new FilesystemAdapter());
    }
}
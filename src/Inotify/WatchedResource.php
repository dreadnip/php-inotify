<?php

declare(strict_types=1);

namespace Inotify;

class WatchedResource
{
    public function __construct(
        private string $pathname,
        private int $watchOnChangeFlags,
        private string $customName
    ) {
    }

    public function getPathname(): string
    {
        return $this->pathname;
    }

    public function getWatchOnChangeFlags(): int
    {
        return $this->watchOnChangeFlags;
    }

    public function getCustomName(): string
    {
        return $this->customName;
    }
}

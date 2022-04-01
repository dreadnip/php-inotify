<?php

declare(strict_types=1);

namespace Inotify;

class WatchedResource
{
    public function __construct(
        private string $pathname,
        private InotifyEventCodeEnum $eventCode,
        private string $customName
    ) {
    }

    public function getPathname(): string
    {
        return $this->pathname;
    }

    public function getEventCode(): InotifyEventCodeEnum
    {
        return $this->eventCode;
    }

    public function getCustomName(): string
    {
        return $this->customName;
    }
}

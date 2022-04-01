<?php

declare(strict_types=1);

namespace Inotify;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InotifyConsumerFactory
{
    public function __construct(
        private InotifyProxyInterface $inotifyProxy = new InotifyProxy(),
        private EventDispatcherInterface $eventDispatcher = new EventDispatcher()
    ) {
    }

    /**
     * @param WatchedResourceCollection|WatchedResource[] $collection
     */
    public function consume(WatchedResourceCollection $collection): void
    {
        foreach ($collection as $resource) {
            $this->inotifyProxy->addWatch($resource);
        }

        while ($events = $this->inotifyProxy->read()) {
            foreach ($events as $event) {
                $this->eventDispatcher->dispatch($event);
            }
        }

        $this->inotifyProxy->closeWatchers();
    }

    public function registerSubscriber(EventSubscriberInterface $eventSubscribers): self
    {
        $this->eventDispatcher->addSubscriber($eventSubscribers);

        return $this;
    }
}
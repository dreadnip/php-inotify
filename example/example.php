<?php

declare(strict_types=1);

use Inotify\InotifyConsumerFactory;
use Inotify\InotifyEvent;
use Inotify\InotifyEventCodeEnum;
use Inotify\WatchedResource;
use Inotify\WatchedResourceCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

include __DIR__ . '/../vendor/autoload.php';

(new InotifyConsumerFactory())
    ->registerSubscriber(
        new class implements EventSubscriberInterface {
            public static function getSubscribedEvents(): array
            {
                return [InotifyEvent::class => 'onInotifyEvent'];
            }

            public function onInotifyEvent(InotifyEvent $event): void
            {
                echo $event;
            }
        }
    )->consume(
        new WatchedResourceCollection([
            new WatchedResource(
                sys_get_temp_dir(),
                // sys_get_temp_dir() . '/test.log',
                //InotifyEventCodeEnum::ON_CREATE->value | InotifyEventCodeEnum::ON_DELETE->value,
                InotifyEventCodeEnum::ON_ALL_EVENTS->value,
                'test'
            )
        ])
    );
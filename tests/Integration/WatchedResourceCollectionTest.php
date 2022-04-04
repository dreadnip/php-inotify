<?php

declare(strict_types=1);

namespace Inotify\Tests\Integration;

use Inotify\InotifyEventCodeEnum;
use Inotify\WatchedResource;
use Inotify\WatchedResourceCollection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class WatchedResourceCollectionTest extends TestCase
{
    public function testInvalidCollectionType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new WatchedResourceCollection(['a string', 5, false]);
    }

    public function testShouldReceiveEventsOnDir(): void
    {
        $paths = [];
        for ($i = 0; $i < 5; $i++) {
            $paths[] = $this->getPath() . DIRECTORY_SEPARATOR . $this->getRandomName();
        }

        $collection = WatchedResourceCollection::fromArray(
            $paths,
            InotifyEventCodeEnum::ON_ALL_EVENTS,
            'test'
        );

        $this->assertCount(5, $collection);
        $this->assertInstanceOf(WatchedResource::class, $collection->first());
    }

    private function getPath(): string
    {
        return sys_get_temp_dir();
    }

    private function getRandomName(): string
    {
        return uniqid('test-', true);
    }

    private function createFile(string $filePath): void
    {
        touch($filePath);
    }

    private function removeFile(string $filePath): void
    {
        unlink($filePath);
    }

    private function createDir(string $filePath): void
    {
        mkdir($filePath);
    }
}
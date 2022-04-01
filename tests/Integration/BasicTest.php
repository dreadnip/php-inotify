<?php

declare(strict_types=1);

namespace Inotify\Tests\Integration;

use Inotify\InotifyEvent;
use Inotify\InotifyEventCodeEnum;
use Inotify\InotifyProxy;
use Inotify\WatchedResource;
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    public function testShouldReceiveEventsOnDir(): void
    {
        $path = $this->getPath();
        $randomFile = $this->getRandomName();
        $randomDir = $this->getRandomName();
        $file = $path . DIRECTORY_SEPARATOR . $randomFile;
        $dir = $path . DIRECTORY_SEPARATOR . $randomDir;
        $custom = uniqid('custom-', true);

        $inotify = new InotifyProxy();
        $inotify->addWatch(
            new WatchedResource(
                $path,
                InotifyEventCodeEnum::ON_ALL_EVENTS,
                $custom
            )
        );

        $this->createFile($file);
        $this->removeFile($file);
        $this->createDir($dir);


        /** @var InotifyEvent[] $events */
        $events = [];
        foreach ($inotify->read() as $inotifyEvent) {
            $events[] = $inotifyEvent;
        }

        self::assertCount(6, $events);
        self::assertEquals(InotifyEventCodeEnum::ON_CREATE, $events[0]->getInotifyEventCode());
        self::assertEquals(InotifyEventCodeEnum::ON_OPEN, $events[1]->getInotifyEventCode());
        self::assertEquals(InotifyEventCodeEnum::ON_CLOSE_WRITE, $events[2]->getInotifyEventCode());
        self::assertEquals(InotifyEventCodeEnum::ON_ATTRIB, $events[3]->getInotifyEventCode());
        self::assertEquals(InotifyEventCodeEnum::ON_DELETE, $events[4]->getInotifyEventCode());
        self::assertEquals(InotifyEventCodeEnum::ON_CREATE_HIGH, $events[5]->getInotifyEventCode());

        $results = $events[0]->toArray();
        unset($results['timestamp']);

        self::assertEquals(
            [
                'id' => 1,
                'eventCode' => InotifyEventCodeEnum::ON_CREATE->value,
                'eventDescription' => InotifyEventCodeEnum::ON_CREATE->getDescription(),
                'uniqueId' => 0,
                'fileName' => $randomFile,
                'pathName' => $path,
                'customName' => $custom,
                'pathWithFile' => $file,
            ],
            $results
        );

        $results = $events[5]->toArray();
        unset($results['timestamp']);

        self::assertEquals(
            [
                'id' => 1,
                'eventCode' => InotifyEventCodeEnum::ON_CREATE_HIGH->value,
                'eventDescription' => InotifyEventCodeEnum::ON_CREATE_HIGH->getDescription(),
                'uniqueId' => 0,
                'fileName' => $randomDir,
                'pathName' => $path,
                'customName' => $custom,
                'pathWithFile' => $dir,
            ],
            $results
        );

        $inotify->closeWatchers();
    }

    public function testShouldReceiveEventsOnFile(): void
    {
        $path = $this->getPath();
        $randomFile = $this->getRandomName();
        $file = $path . DIRECTORY_SEPARATOR . $randomFile;
        $custom = uniqid('custom-', true);

        $this->createFile($file);

        $inotify = new InotifyProxy();
        $inotify->addWatch(
            new WatchedResource(
                $file,
                InotifyEventCodeEnum::ON_ALL_EVENTS,
                $custom
            )
        );

        $this->removeFile($file);

        /** @var InotifyEvent[] $events */
        $events = [];
        foreach ($inotify->read() as $inotifyEvent) {
            $events[] = $inotifyEvent;
        }

        self::assertCount(3, $events);
        self::assertEquals(InotifyEventCodeEnum::ON_ATTRIB, $events[0]->getInotifyEventCode());
        self::assertEquals(InotifyEventCodeEnum::ON_DELETE_SELF, $events[1]->getInotifyEventCode());
        self::assertEquals(InotifyEventCodeEnum::ON_IGNORED, $events[2]->getInotifyEventCode());

        $results = $events[0]->toArray();
        unset($results['timestamp']);

        self::assertEquals(
            [
                'id' => 1,
                'eventCode' => InotifyEventCodeEnum::ON_ATTRIB->value,
                'eventDescription' => InotifyEventCodeEnum::ON_ATTRIB->getDescription(),
                'uniqueId' => 0,
                'fileName' => '',
                'pathName' => $file,
                'customName' => $custom,
                'pathWithFile' => $file,
            ],
            $results
        );

        $inotify->closeWatchers();
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
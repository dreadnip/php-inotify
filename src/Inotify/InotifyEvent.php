<?php

declare(strict_types=1);

namespace Inotify;

use JsonSerializable;

class InotifyEvent implements JsonSerializable
{
    public function __construct(
        private int $id,
        private InotifyEventCodeEnum $inotifyEventCodeEnum,
        private int $uniqueId,
        private string $fileName,
        private WatchedResource $watchedResource,
        private int $timestamp
    ) {
    }

    public function __toString(): string
    {
        return (string) print_r($this->toArray(), true);
    }

    /**
     * @return array<string, string|int>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'eventCode' => $this->getInotifyEventCode()->value,
            'eventDescription' => $this->getInotifyEventCodeDescription(),
            'uniqueId' => $this->getUniqueId(),
            'fileName' => $this->getFileName(),
            'pathName' => $this->getWatchedResource()->getPathname(),
            'customName' => $this->getWatchedResource()->getCustomName(),
            'pathWithFile' => $this->getPathWithFile(),
            'timestamp' => $this->getTimestamp()
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getInotifyEventCode(): InotifyEventCodeEnum
    {
        return $this->inotifyEventCodeEnum;
    }

    public function getInotifyEventCodeDescription(): string
    {
        return $this->inotifyEventCodeEnum->getDescription();
    }

    public function getUniqueId(): int
    {
        return $this->uniqueId;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getWatchedResource(): WatchedResource
    {
        return $this->watchedResource;
    }

    public function getPathWithFile(): string
    {
        $path = $this->watchedResource->getPathname();
        if ('' === $this->getFileName()) {
            return $path;
        }

        if (!str_starts_with($this->getFileName(), '/')) {
            return $path . DIRECTORY_SEPARATOR . $this->getFileName();
        }

        return $path . $this->getFileName();
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return array<string, string|int>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

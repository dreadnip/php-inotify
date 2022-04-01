<?php

declare(strict_types=1);

namespace Inotify;

use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

class WatchedResourceCollection extends ArrayCollection
{
    public function __construct(array $elements = [])
    {
        foreach ($elements as $element) {
            if (!$element instanceof WatchedResource) {
                throw new InvalidArgumentException('Invalid collection member type.');
            }
        }

        parent::__construct($elements);
    }

    public function set($key, $value): void
    {
        if (!$value instanceof WatchedResource) {
            throw new InvalidArgumentException('Invalid collection member type.');
        }

        parent::set($key, $value);
    }

    public function add($element): bool
    {
        if (!$element instanceof WatchedResource) {
            throw new InvalidArgumentException('Invalid collection member type.');
        }

        return parent::add($element);
    }

    public static function fromArray(array $paths, InotifyEventCodeEnum $flag, string $customName): self
    {
        $collection = new self();

        foreach ($paths as $path) {
            $collection->add(
                new WatchedResource($path, $flag, $customName)
            );
        }

        return $collection;
    }
}

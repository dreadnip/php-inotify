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

    public function set($key, $value)
    {
        if (!$value instanceof WatchedResource) {
            throw new InvalidArgumentException('Invalid collection member type.');
        }

        parent::set($key, $value);
    }

    public function add($element)
    {
        if (!$element instanceof WatchedResource) {
            throw new InvalidArgumentException('Invalid collection member type.');
        }

        parent::add($element);
    }
}
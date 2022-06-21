<?php
namespace PhpMemoryCache\Collection;

class SomeEntityCollection extends ArrayCollection
{
    protected $itemType = SomeEntity::class;
}

<?php
namespace PhpMemoryCache\Collection;

class ClientCode
{
    public function __construct(){
        $someEntity = new SomeEntity();
        $someEntityCollection = new SomeEntityCollection();
        $someEntityCollection->add($someEntity);
    }
}
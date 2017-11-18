<?php

namespace Rnr\Alice\Populators;

interface PopulatorInterface
{
    public function can(&$object, $propertyPath, $value): bool;
    public function setValue(&$object, $property, $value);
}
<?php

namespace Pulunomoe\DataMapper\Tests;

use Pulunomoe\DataMapper\Entity;
use Pulunomoe\DataMapper\EntityClass;
use Pulunomoe\DataMapper\Property;

#[Entity('tests')]
class TestEntity extends EntityClass
{
	#[Property('id')]
	public int $id;

	#[Property('name')]
	public string $name;

	#[Property('description')]
	public string $description;
}
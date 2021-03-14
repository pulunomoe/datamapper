<?php

namespace Pulunomoe\DataMapper\Tests;

use Pulunomoe\DataMapper\EntityClass;
use Pulunomoe\DataMapper\Attribute\Entity;
use Pulunomoe\DataMapper\Attribute\Property;

#[Entity('tests')]
class TestEntity extends EntityClass
{
	#[Property('id_field')]
	public int $id;

	#[Property('name_field')]
	public string $name;

	#[Property('description_field')]
	public string $description;
}
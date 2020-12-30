<?php

namespace Pulunomoe\DataMapper\Tests;

use Pulunomoe\DataMapper\Entity;
use Pulunomoe\DataMapper\EntityClass;
use Pulunomoe\DataMapper\Property;

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
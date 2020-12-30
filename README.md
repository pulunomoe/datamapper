# pulunomoe/datamapper

Super simple data mapper ORM for PHP.

## Requirements

- PHP 8+
- PDO

## Features

- CRUD (wow!)
- More incoming (soon-ish)

## Usage

### 1. Install with composer

`composer require pulunomoe/datamapper`

### 2. Create your entity class

```php
<?php

// src/Entity/Car.php

namespace YourApp\Entity;

use Pulunomoe\DataMapper\Entity;
use Pulunomoe\DataMapper\EntityClass;
use Pulunomoe\DataMapper\Property;

#[Entity('tests')] // Put your table name here
class Car extends EntityClass
{
	#[Property('id')] // Put your column name here
	public int $id;   // "$id" is a special column for primary key

	#[Property('brand')]
	public string $brand;

	#[Property('model')]
	public string $model;
}
```

### 3. Call the DataMapper

```php
<?php

// Initialize the data mapper by passing a PDO instance and the entity class name
$dm = new DataMapper($pdo, Car::class);

// Retrieve all cars
$dm->findAll();

// Retrieve all cars by brand
$dm->findAllBy('brand', 'Danke Motoren Werke');

// Retrieve a single car with the id = 1
$dm->findOne(1);

// Save a car to the database
$car = new Car();
$car->brand = 'Honyabishi';
$car->model = 'Super 9001';
$car = $dm->create($car);

// Update a car
$car->model = 'Super 9001 Mark II Type R GT-MAXXX';
$car = $dm->update($car);

// Delete a car with the id = 1
$dm->delete(1);
```

## API

- Find All

`findAll(string $orderBy = '', bool $desc = false, int $limit = 10, int $offset = 0): array`

- Find All By

`findAllBy(string $column, string $value, string $orderBy = '', bool $desc = false, int $limit = 10, int $offset = 0): array`

- Find One

`function findOne(int $id): ?EntityClass`

- Create

`create(EntityClass $object): EntityClass`

- Update

`update(EntityClass $object): EntityClass`

- Delete

`function delete(int $id): void`

## Changelog

- v0.1 : Initial version
- v0.2 : Added ordering, limit, and find all by

## Shameless plug

Like this library? [Buy me some coffee](https://ko-fi.com/pulunomoe) or [buy me some cendol](https://trakteer.id/pulunomoe)

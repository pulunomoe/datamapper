# pulunomoe/datamapper

Super simple data mapper ORM for PHP 8.

## Requirements

- PHP 8+
- PDO

## Features

- CRUD (wow!)
- Validations  
- More incoming (soon-ish)

## Usage

### 1. Install with composer

`composer require pulunomoe/datamapper`

### 2. Create your entity class

```php
<?php

// src/EntityClass/Car.php

namespace YourApp\Entity;

use Pulunomoe\DataMapper\EntityClass;
use Pulunomoe\DataMapper\Attribute\Entity;
use Pulunomoe\DataMapper\Attribute\Property;
use Pulunomoe\DataMapper\Validator\NotEmpty;
use Pulunomoe\DataMapper\Validator\Unique;

#[Entity('tests')] // Put your table name here
class Car extends EntityClass
{
	#[Property('id')] // Put your column name here
	public int $id;   // "$id" is a special column for primary key

	#[Property('brand')]
	#[Validator([NotEmpty::class])] // This field is validated
	public string $brand;

	#[Property('model')]
	#[Validator([NotEmpty::class, Unique::class])] // This field is using multiple validation
	public string $model;
}
```

### 3. Call the DataMapper

```php
<?php

use Pulunomoe\DataMapper\ValidationException;

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
$car = $dm->create($car); // If the data is invalid, an exception will be thrown

// Update a car
$car->model = 'Super 9001 Mark II Type R GT-MAXXX';
$car = $dm->update($car); // If the data is invalid, an exception will be thrown

// Delete a car with the id = 1
$dm->delete(1);

// Catching validation exception
try {
    $dm->create($car);
} catch (ValidationException $ex) {
    $errors = $ex->getErrors();
}
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
- v0.3 : Added basic validations

## Shameless plug

Like this library? [Buy me some coffee](https://ko-fi.com/pulunomoe) or [buy me some cendol](https://trakteer.id/pulunomoe)

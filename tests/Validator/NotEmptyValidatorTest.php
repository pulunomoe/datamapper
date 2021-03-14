<?php

namespace Pulunomoe\DataMapper\Tests\Validator;

use PDO;
use Pulunomoe\DataMapper\Attribute\Entity;
use Pulunomoe\DataMapper\Attribute\Property;
use Pulunomoe\DataMapper\Attribute\Validator;
use Pulunomoe\DataMapper\DataMapper;
use Pulunomoe\DataMapper\EntityClass;
use Pulunomoe\DataMapper\ValidationException;
use Pulunomoe\DataMapper\Validator\NotEmpty;

class NotEmptyValidatorTest extends ValidatorTest
{
	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();
		self::$pdo->exec('CREATE TABLE validator_not_empty (id_field INTEGER PRIMARY KEY, test_field TEXT NOT NULL)');
	}

	protected function setUp(): void
	{
		self::$pdo->exec('DELETE FROM validator_not_empty');
	}

	public function testCreateValidatorValid()
	{
		// GIVEN I have a valid entity
		$entity = new NotEmptyValidatorTestEntity();
		$entity->test = 'notEmpty';

		// WHEN I try to create the entity
		$dm = new DataMapper(self::$pdo, NotEmptyValidatorTestEntity::class);
		$dm->create($entity);

		// THEN I should get no error
		$this->assertTrue(true);
	}

	public function testCreateValidatorInvalid()
	{
		$this->expectException(ValidationException::class);

		// GIVEN I have an invalid entity
		$entity = new NotEmptyValidatorTestEntity();
		$entity->test = '';

		// WHEN I try to create the entity
		$dm = new DataMapper(self::$pdo, NotEmptyValidatorTestEntity::class);
		$dm->create($entity);

		// THEN I should get an exception
	}

	public function testUpdateValidatorValid()
	{
		// GIVEN I have an entity stored in the database
		$entity = new NotEmptyValidatorTestEntity();
		$entity->test = 'old';

		$dm = new DataMapper(self::$pdo, NotEmptyValidatorTestEntity::class);
		$entity = $dm->create($entity);

		// WHEN I try to update the entity with valid data
		$entity->test = 'new';
		$dm->update($entity);

		// THEN I should get no error
		$this->assertTrue(true);
	}

	public function testUpdateValidatorInvalid()
	{
		$this->expectException(ValidationException::class);

		// GIVEN I have an entity stored in the database
		$entity = new NotEmptyValidatorTestEntity();
		$entity->test = 'old';

		$dm = new DataMapper(self::$pdo, NotEmptyValidatorTestEntity::class);
		$entity = $dm->create($entity);

		// WHEN I try to update the entity with invalid data
		$entity->test = '';
		$dm->update($entity);

		// THEN I should get an exception
	}
}

#[Entity('validator_not_empty')]
class NotEmptyValidatorTestEntity extends EntityClass
{
	#[Property('id_field')]
	public int $id;

	#[Property('test_field')]
	#[Validator([NotEmpty::class])]
	public string $test;
}
<?php

namespace Pulunomoe\DataMapper\Tests\Validator;

use PDO;
use PHPUnit\Framework\TestCase;

abstract class ValidatorTest extends TestCase
{
	protected static PDO $pdo;

	public static function setUpBeforeClass(): void
	{
		self::$pdo = new PDO('sqlite::memory:');
	}

	abstract public function testCreateValidatorValid();

	abstract public function testCreateValidatorInvalid();

	abstract public function testUpdateValidatorValid();

	abstract public function testUpdateValidatorInvalid();
}
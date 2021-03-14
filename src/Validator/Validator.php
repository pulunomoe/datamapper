<?php

namespace Pulunomoe\DataMapper\Validator;

use PDO;

interface Validator
{
	public static function validateForCreate(PDO $pdo, mixed $input): ?string;

	public static function validateForUpdate(PDO $pdo, mixed $input): ?string;
}
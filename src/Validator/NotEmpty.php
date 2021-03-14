<?php

namespace Pulunomoe\DataMapper\Validator;

use PDO;

class NotEmpty implements Validator
{
	public static function validateForCreate(PDO $pdo, mixed $input): ?string
	{
		return self::validate($input);
	}

	public static function validateForUpdate(PDO $pdo, mixed $input): ?string
	{
		return self::validate($input);
	}

	private static function validate(mixed $input): ?string
	{
		return !empty($input) ? null : 'must not be empty';
	}
}
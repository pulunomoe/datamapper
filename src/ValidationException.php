<?php

namespace Pulunomoe\DataMapper;

use Exception;
use Throwable;

class ValidationException extends Exception
{
	private array $errors;

	public function __construct(array $errors, int $code = 0, Throwable $previous = null)
	{
		parent::__construct('datamapper validation exception', $code, $previous);
	}

	public function getErrors(): array
	{
		return $this->errors;
	}

	public function setErrors(array $errors): void
	{
		$this->errors = $errors;
	}
}
<?php

namespace Pulunomoe\DataMapper\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Validator
{
	private array $validators;

	public function __construct(array $validators)
	{
		$this->validators = $validators;
	}

	public function getValidators(): array
	{
		return $this->validators;
	}

	public function setValidators(array $validators): void
	{
		$this->validators = $validators;
	}
}
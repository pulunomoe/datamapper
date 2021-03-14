<?php

namespace Pulunomoe\DataMapper\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Property
{
	private string $column;

	public function __construct(string $column)
	{
		$this->column = $column;
	}

	public function getColumn(): string
	{
		return $this->column;
	}

	public function setColumn(string $column): void
	{
		$this->column = $column;
	}
}
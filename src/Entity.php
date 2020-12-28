<?php

namespace Pulunomoe\DataMapper;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Entity
{
	private string $table;

	public function __construct(string $table)
	{
		$this->table = $table;
	}

	public function getTable(): string
	{
		return $this->table;
	}

	public function setTable(string $table): void
	{
		$this->table = $table;
	}
}
<?php

namespace Pulunomoe\DataMapper;

class EntityClass
{
	public function __toString(): string
	{
		return json_encode($this);
	}
}
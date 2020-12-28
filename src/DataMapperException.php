<?php

namespace Pulunomoe\DataMapper;

use Exception;
use Throwable;

class DataMapperException extends Exception
{
	public function __construct($message = '', $code = 0, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
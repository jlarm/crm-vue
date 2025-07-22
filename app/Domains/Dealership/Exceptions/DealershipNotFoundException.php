<?php

namespace App\Domains\Dealership\Exceptions;

use Exception;

class DealershipNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Dealership with ID {$id} not found.");
    }
}

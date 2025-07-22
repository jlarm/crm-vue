<?php

namespace App\Domains\Dealership\Models;

use App\Enums\DevStatus;
use App\Enums\Rating;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;

class Dealership extends Model
{
    protected function casts(): array
    {
        return [
            'status' => Status::class,
            'rating' => Rating::class,
            'in_development' => 'boolean',
            'dev_status' => DevStatus::class,
        ];
    }
}

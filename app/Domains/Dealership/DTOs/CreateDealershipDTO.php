<?php

namespace App\Domains\Dealership\DTOs;

use App\Enums\DevStatus;

class CreateDealershipDTO
{
    public function __construct(
        public int $userId,
        public string $name,
        public string $address,
        public string $city,
        public string $state,
        public string $zipCode,
        public string $phone,
        public string $email,
        public ?string $currentSolutionName = null,
        public ?string $currentSolutionUse = null,
        public ?string $notes = null,
        public ?string $status = null,
        public ?int $rating = null,
        public ?string $type = null,
        public ?bool $inDevelopment = false,
        public ?DevStatus $devStatus = null,
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zipCode,
            'phone' => $this->phone,
            'email' => $this->email,
            'current_solution_name' => $this->currentSolutionName,
            'current_solution_use' => $this->currentSolutionUse,
            'notes' => $this->notes,
            'status' => $this->status,
            'rating' => $this->rating,
            'type' => $this->type,
            'in_development' => $this->inDevelopment,
            'dev_status' => $this->devStatus,
        ];
    }
}

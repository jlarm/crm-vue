<?php

namespace App\Domains\Dealership\DTOs;

use App\Enums\DevStatus;

class UpdateDealershipDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $zipCode = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $currentSolutionName = null,
        public ?string $currentSolutionUse = null,
        public ?string $notes = null,
        public ?string $status = null,
        public ?int $rating = null,
        public ?string $type = null,
        public ?bool $inDevelopment = null,
        public ?DevStatus $devStatus = null,
    ) {}

    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }
        if ($this->address !== null) {
            $data['address'] = $this->address;
        }
        if ($this->city !== null) {
            $data['city'] = $this->city;
        }
        if ($this->state !== null) {
            $data['state'] = $this->state;
        }
        if ($this->zipCode !== null) {
            $data['zip_code'] = $this->zipCode;
        }
        if ($this->phone !== null) {
            $data['phone'] = $this->phone;
        }
        if ($this->email !== null) {
            $data['email'] = $this->email;
        }
        if ($this->currentSolutionName !== null) {
            $data['current_solution_name'] = $this->currentSolutionName;
        }
        if ($this->currentSolutionUse !== null) {
            $data['current_solution_use'] = $this->currentSolutionUse;
        }
        if ($this->notes !== null) {
            $data['notes'] = $this->notes;
        }
        if ($this->status !== null) {
            $data['status'] = $this->status;
        }
        if ($this->rating !== null) {
            $data['rating'] = $this->rating;
        }
        if ($this->type !== null) {
            $data['type'] = $this->type;
        }
        if ($this->inDevelopment !== null) {
            $data['in_development'] = $this->inDevelopment;
        }
        if ($this->devStatus !== null) {
            $data['dev_status'] = $this->devStatus;
        }

        return $data;
    }
}

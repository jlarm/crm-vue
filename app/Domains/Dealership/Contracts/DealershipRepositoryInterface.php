<?php

namespace App\Domains\Dealership\Contracts;

use App\Domains\Dealership\DTOs\CreateDealershipDTO;
use App\Domains\Dealership\DTOs\UpdateDealershipDTO;
use App\Domains\Dealership\Models\Dealership;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface DealershipRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Dealership;

    public function findOrFail(int $id): Dealership;

    public function create(CreateDealershipDTO $dto): Dealership;

    public function update(int $id, UpdateDealershipDTO $dto): Dealership;

    public function delete(int $id): bool;

    public function findByUser(int $userId): Collection;

    public function findByType(string $type): Collection;

    public function findByDevStatus(string $devStatus): Collection;

    public function search(string $query): Collection;
}

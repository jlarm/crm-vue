<?php

namespace App\Domains\Dealership\Repositories;

use App\Domains\Dealership\Contracts\DealershipRepositoryInterface;
use App\Domains\Dealership\DTOs\CreateDealershipDTO;
use App\Domains\Dealership\DTOs\UpdateDealershipDTO;
use App\Domains\Dealership\Models\Dealership;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DealershipRepository implements DealershipRepositoryInterface
{
    public function all(): Collection
    {
        return Dealership::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Dealership::with(['users', 'stores', 'contacts'])
            ->paginate($perPage);
    }

    public function find(int $id): ?Dealership
    {
        return Dealership::with(['users', 'stores', 'contacts', 'progresses'])
            ->find($id);
    }

    public function findOrFail(int $id): Dealership
    {
        return Dealership::with(['users', 'stores', 'contacts', 'progresses'])
            ->findOrFail($id);
    }

    public function create(CreateDealershipDTO $dto): Dealership
    {
        return Dealership::create($dto->toArray());
    }

    public function update(int $id, UpdateDealershipDTO $dto): Dealership
    {
        $dealership = $this->findOrFail($id);
        $dealership->update($dto->toArray());

        return $dealership->fresh(['users', 'stores', 'contacts', 'progresses']);
    }

    public function delete(int $id): bool
    {
        $dealership = $this->findOrFail($id);

        return $dealership->delete();
    }

    public function findByUser(int $userId): Collection
    {
        return Dealership::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['users', 'stores', 'contacts'])->get();
    }

    public function findByType(string $type): Collection
    {
        return Dealership::where('type', $type)
            ->with(['users', 'stores', 'contacts'])
            ->get();
    }

    public function findByDevStatus(string $devStatus): Collection
    {
        return Dealership::where('dev_status', $devStatus)
            ->with(['users', 'stores', 'contacts'])
            ->get();
    }

    public function search(string $query): Collection
    {
        return Dealership::where('name', 'LIKE', "%{$query}%")
            ->orWhere('city', 'LIKE', "%{$query}%")
            ->orWhere('state', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->with(['users', 'stores', 'contacts'])
            ->get();
    }
}

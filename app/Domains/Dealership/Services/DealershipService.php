<?php

namespace App\Domains\Dealership\Services;

use App\Domains\Dealership\Contracts\DealershipRepositoryInterface;
use App\Domains\Dealership\DTOs\CreateDealershipDTO;
use App\Domains\Dealership\DTOs\UpdateDealershipDTO;
use App\Domains\Dealership\Events\DealershipCreated;
use App\Domains\Dealership\Events\DealershipUpdated;
use App\Domains\Dealership\Models\Dealership;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DealershipService
{
    public function __construct(
        private readonly DealershipRepositoryInterface $repository
    ) {}

    public function getAllDealerships(): Collection
    {
        return $this->repository->all();
    }

    public function getPaginatedDealerships(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function getDealership(int $id): ?Dealership
    {
        return $this->repository->find($id);
    }

    public function createDealership(CreateDealershipDTO $dto): Dealership
    {
        $dealership = $this->repository->create($dto);

        event(new DealershipCreated($dealership));

        return $dealership;
    }

    public function updateDealership(int $id, UpdateDealershipDTO $dto): Dealership
    {
        $originalDealership = $this->repository->findOrFail($id);
        $updatedDealership = $this->repository->update($id, $dto);

        event(new DealershipUpdated($originalDealership, $updatedDealership));

        return $updatedDealership;
    }

    public function deleteDealership(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getDealershipsByUser(int $userId): Collection
    {
        return $this->repository->findByUser($userId);
    }

    public function getDealershipsByType(string $type): Collection
    {
        return $this->repository->findByType($type);
    }

    public function getDealershipsByDevStatus(string $devStatus): Collection
    {
        return $this->repository->findByDevStatus($devStatus);
    }

    public function searchDealerships(string $query): Collection
    {
        return $this->repository->search($query);
    }

    public function assignUserToDealership(int $dealershipId, int $userId): Dealership
    {
        $dealership = $this->repository->findOrFail($dealershipId);
        $dealership->users()->attach($userId);

        return $dealership->fresh(['users']);
    }

    public function removeUserFromDealership(int $dealershipId, int $userId): Dealership
    {
        $dealership = $this->repository->findOrFail($dealershipId);
        $dealership->users()->detach($userId);

        return $dealership->fresh(['users']);
    }

    public function calculateDealershipMetrics(int $dealershipId): array
    {
        $dealership = $this->repository->findOrFail($dealershipId);

        return [
            'total_stores' => $dealership->stores()->count(),
            'total_contacts' => $dealership->contacts()->count(),
            'active_progresses' => $dealership->progresses()->whereNull('completed_at')->count(),
            'completion_rate' => $this->calculateCompletionRate($dealership),
            'last_activity' => $dealership->progresses()->latest()->first()?->created_at,
        ];
    }

    private function calculateCompletionRate(Dealership $dealership): float
    {
        $totalProgresses = $dealership->progresses()->count();

        if ($totalProgresses === 0) {
            return 0.0;
        }

        $completedProgresses = $dealership->progresses()->whereNotNull('completed_at')->count();

        return round(($completedProgresses / $totalProgresses) * 100, 2);
    }
}

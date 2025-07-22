<?php

namespace App\Domains\Dealership\Http\Controllers;

use App\Domains\Dealership\DTOs\CreateDealershipDTO;
use App\Domains\Dealership\DTOs\UpdateDealershipDTO;
use App\Domains\Dealership\Http\Requests\CreateDealershipRequest;
use App\Domains\Dealership\Http\Requests\UpdateDealershipRequest;
use App\Domains\Dealership\Services\DealershipService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DealershipController extends Controller
{
    public function __construct(
        private readonly DealershipService $dealershipService
    ) {}

    public function index(Request $request): Response
    {
        $dealerships = $this->dealershipService->getAllDealerships();

        return Inertia::render('dealerships/Dashboard', [
            'dealerships' => $dealerships,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $dealership = $this->dealershipService->getDealership($id);

        if (! $dealership) {
            return response()->json([
                'message' => 'Dealership not found',
            ], 404);
        }

        return response()->json([
            'data' => $dealership,
            'message' => 'Dealership retrieved successfully',
        ]);
    }

    public function store(CreateDealershipRequest $request): JsonResponse
    {
        $dto = new CreateDealershipDTO(
            userId: $request->validated('user_id'),
            name: $request->validated('name'),
            address: $request->validated('address'),
            city: $request->validated('city'),
            state: $request->validated('state'),
            zipCode: $request->validated('zip_code'),
            phone: $request->validated('phone'),
            email: $request->validated('email'),
            currentSolutionName: $request->validated('current_solution_name'),
            currentSolutionUse: $request->validated('current_solution_use'),
            notes: $request->validated('notes'),
            status: $request->validated('status'),
            rating: $request->validated('rating'),
            type: $request->validated('type'),
            inDevelopment: $request->validated('in_development', false),
            devStatus: $request->validated('dev_status'),
        );

        $dealership = $this->dealershipService->createDealership($dto);

        return response()->json([
            'data' => $dealership,
            'message' => 'Dealership created successfully',
        ], 201);
    }

    public function update(UpdateDealershipRequest $request, int $id): JsonResponse
    {
        $dto = new UpdateDealershipDTO(
            name: $request->validated('name'),
            address: $request->validated('address'),
            city: $request->validated('city'),
            state: $request->validated('state'),
            zipCode: $request->validated('zip_code'),
            phone: $request->validated('phone'),
            email: $request->validated('email'),
            currentSolutionName: $request->validated('current_solution_name'),
            currentSolutionUse: $request->validated('current_solution_use'),
            notes: $request->validated('notes'),
            status: $request->validated('status'),
            rating: $request->validated('rating'),
            type: $request->validated('type'),
            inDevelopment: $request->validated('in_development'),
            devStatus: $request->validated('dev_status'),
        );

        $dealership = $this->dealershipService->updateDealership($id, $dto);

        return response()->json([
            'data' => $dealership,
            'message' => 'Dealership updated successfully',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->dealershipService->deleteDealership($id);

        return response()->json([
            'message' => 'Dealership deleted successfully',
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $dealerships = $this->dealershipService->searchDealerships($query);

        return response()->json([
            'data' => $dealerships,
            'message' => 'Search completed successfully',
        ]);
    }

    public function metrics(int $id): JsonResponse
    {
        $metrics = $this->dealershipService->calculateDealershipMetrics($id);

        return response()->json([
            'data' => $metrics,
            'message' => 'Dealership metrics retrieved successfully',
        ]);
    }

    public function assignUser(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $dealership = $this->dealershipService->assignUserToDealership(
            $id,
            $request->get('user_id')
        );

        return response()->json([
            'data' => $dealership,
            'message' => 'User assigned to dealership successfully',
        ]);
    }

    public function removeUser(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $dealership = $this->dealershipService->removeUserFromDealership(
            $id,
            $request->get('user_id')
        );

        return response()->json([
            'data' => $dealership,
            'message' => 'User removed from dealership successfully',
        ]);
    }
}

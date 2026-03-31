<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $users = null;

        if (auth()->user()->isAdmin()) {
            $users = User::withTrashed()->paginate();
        }

        if (auth()->user()->isDoctor()) {
            $users = User::query()->paginate();
        }

        return $this->paginated('Users retrieved successfully.', UserResource::collection($users));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $user = User::create($request->validated());

        return $this->created('User created successfully.', [
            'user' => new UserResource($user),
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $user = auth()->user()->isAdmin() ? User::withTrashed()->findOrFail($id) : User::findOrFail($id);
        $user->load('patient');

        $this->authorize('view', $user);

        return $this->ok('User retrieved', [
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $this->authorize('update', $user);

        $user->update($request->validated());

        return $this->ok('User updated successfully.', [
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $this->authorize('delete', $user);

        $user->delete();

        Log::warning('security.user_deactivated', [
            'by' => auth()->id(),
            'target' => $user->id,
            'ip' => request()->ip(),
        ]);

        return $this->noContent();
    }

    /**
     * Restore a soft-deleted resource.
     */
    public function restore(string $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        $this->authorize('restore', $user);

        $user->restore();

        Log::warning('security.user_restored', [
            'by' => auth()->id(),
            'target' => $user->id,
            'ip' => request()->ip(),
        ]);

        return $this->ok('User restored successfully.', [
            'user' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Permanently delete a resource.
     */
    public function forceDelete(string $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $user);

        $user->forceDelete();

        Log::warning('security.user_permanently_deleted', [
            'by' => auth()->id(),
            'target' => $user->id,
            'ip' => request()->ip(),
        ]);

        return $this->noContent();
    }
}

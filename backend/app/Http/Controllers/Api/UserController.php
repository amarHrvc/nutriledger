<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\JsonResponse;

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $user = User::where('id', $id)->first();

        if (!$user) return $this->notFound();

        return $this->ok('OK', [
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

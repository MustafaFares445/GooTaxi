<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use Throwable;
use App\Models\User;
use App\Data\UserData;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserFilterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserSummaryResource;
use Symfony\Component\HttpFoundation\Response;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final readonly class UserController
{
    public function __construct(private UserService $userService) {}

    /**
     * @tags Dashboard
     */
    public function index(UserFilterRequest $request): AnonymousResourceCollection
    {
        $users = User::getQuery()
            ->paginate($request->get('perPage', 20));

        return UserResource::collection($users)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @tags Dashboard
     */
    public function summary(UserFilterRequest $request): AnonymousResourceCollection
    {
        $users = User::getQuery()
            ->select(['id' , 'name'])
            ->get();

        return UserSummaryResource::collection($users)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @tags Dashboard
     *
     * @throws Throwable
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        $user = $this->userService->store(UserData::from($request->validated()));

        return UserResource::make($user)
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @tags Dashboard
     */
    public function show(User $user): UserResource
    {
        return UserResource::make($user)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @tags Dashboard
     *
     * @throws Throwable
     */
    public function update(UserUpdateRequest $request, User $user): UserResource
    {
        $updatedUser = $this->userService->update(UserData::from($request->validated()), $user);

        return UserResource::make($updatedUser)
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * @tags Dashboard
     */
    public function destroy(User $user): UserResource
    {
        $user->delete();

        return UserResource::make($user)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}

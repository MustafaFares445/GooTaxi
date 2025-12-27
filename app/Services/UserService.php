<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\UserData;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final class UserService
{
    /**
     * Validate user data.
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function store(UserData $data): User
    {
        return DB::transaction(static function () use ($data) {
            return User::create($data->onlyModelAttributes());
        });
    }

    /**
     * Update user data
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function update(UserData $data, User $user): User
    {
        return DB::transaction(static function () use ($data, $user) {
            tap($user)->update($data->onlyModelAttributes());

            return $user;
        });
    }
}

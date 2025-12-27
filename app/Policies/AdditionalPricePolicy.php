<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AdditionalPrice;
use App\Models\User;
use Mrmarchone\LaravelAutoCrud\Traits\AuthorizesByPermissionGroup;

final class AdditionalPricePolicy
{
    use AuthorizesByPermissionGroup;

    public function viewAny(User $user): bool
    {
        return $this->authorizeAction($user, 'view');
    }

    public function view(User $user, AdditionalPrice $additionalPrice): bool
    {
        return $this->authorizeAction($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->authorizeAction($user, 'create');
    }

    public function update(User $user, AdditionalPrice $additionalPrice): bool
    {
        return $this->authorizeAction($user, 'update');
    }

    public function delete(User $user, AdditionalPrice $additionalPrice): bool
    {
        return $this->authorizeAction($user, 'delete');
    }
}

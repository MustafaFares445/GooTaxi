<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Offer;
use App\Models\User;
use Mrmarchone\LaravelAutoCrud\Traits\AuthorizesByPermissionGroup;

final class OfferPolicy
{
    use AuthorizesByPermissionGroup;

    public function viewAny(User $user): bool
    {
        return $this->authorizeAction($user, 'view');
    }

    public function view(User $user, Offer $offer): bool
    {
        return $this->authorizeAction($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->authorizeAction($user, 'create');
    }

    public function update(User $user, Offer $offer): bool
    {
        return $this->authorizeAction($user, 'update');
    }

    public function delete(User $user, Offer $offer): bool
    {
        return $this->authorizeAction($user, 'delete');
    }
}

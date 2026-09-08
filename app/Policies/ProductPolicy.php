<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Team;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can view the team's products.
     */
    public function viewAny(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    /**
     * Determine whether the user can create a product for the team.
     */
    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    /**
     * Determine whether the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->belongsToTeam($product->team);
    }

    /**
     * Determine whether the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->belongsToTeam($product->team);
    }
}

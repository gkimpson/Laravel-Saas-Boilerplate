<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\SaveProductRequest;
use App\Models\Product;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the current team's products.
     */
    public function index(Team $current_team): Response
    {
        Gate::authorize('viewAny', [Product::class, $current_team]);

        return Inertia::render('demonstration/products/Index', [
            'products' => $current_team->products()->latest()->get(),
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(SaveProductRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', [Product::class, $current_team]);

        $current_team->products()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Product created.')]);

        return to_route('products.index', $current_team);
    }

    /**
     * Update the specified product.
     */
    public function update(SaveProductRequest $request, Team $current_team, Product $product): RedirectResponse
    {
        abort_unless($product->team_id === $current_team->id, 404);

        Gate::authorize('update', $product);

        $product->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Product updated.')]);

        return to_route('products.index', $current_team);
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Team $current_team, Product $product): RedirectResponse
    {
        abort_unless($product->team_id === $current_team->id, 404);

        Gate::authorize('delete', $product);

        $product->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Product deleted.')]);

        return to_route('products.index', $current_team);
    }
}

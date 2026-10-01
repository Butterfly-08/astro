<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    protected function userId(): int
    {
        return Auth::guard('web')->id();
    }

    // -------------------------------------------------------------------------
    // Wishlist Index
    // -------------------------------------------------------------------------

    /**
     * Show the user's wishlist.
     */
    public function index(): View
    {
        $wishlistItems = Wishlist::with('product.category')
            ->where('user_id', $this->userId())
            ->latest()
            ->paginate(16);

        return view('user.wishlist.index', compact('wishlistItems'));
    }

    // -------------------------------------------------------------------------
    // Toggle (Add / Remove)
    // -------------------------------------------------------------------------

    /**
     * Toggle a product in the user's wishlist.
     * Supports AJAX and standard form submissions.
     */
    public function toggle(Request $request, int $productId): JsonResponse|RedirectResponse
    {
        $product = Product::active()->find($productId);

        if (!$product) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
            }
            return back()->with('error', 'Product not found.');
        }

        $userId = $this->userId();

        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $inWishlist = false;
            $message    = '"' . $product->name . '" removed from your wishlist.';
        } else {
            Wishlist::create([
                'user_id'    => $userId,
                'product_id' => $productId,
            ]);
            $inWishlist = true;
            $message    = '"' . $product->name . '" added to your wishlist!';
        }

        if ($request->ajax()) {
            return response()->json([
                'success'     => true,
                'in_wishlist' => $inWishlist,
                'message'     => $message,
                'count'       => Wishlist::where('user_id', $userId)->count(),
            ]);
        }

        return back()->with('success', $message);
    }

    // -------------------------------------------------------------------------
    // Remove (explicit delete for wishlist page)
    // -------------------------------------------------------------------------

    /**
     * Remove a product from the wishlist by wishlist record ID.
     */
    public function remove(Request $request, int $wishlistId): JsonResponse|RedirectResponse
    {
        $item = Wishlist::where('user_id', $this->userId())
            ->where('id', $wishlistId)
            ->firstOrFail();

        $productName = $item->product?->name ?? 'Product';
        $item->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '"' . $productName . '" removed from wishlist.',
                'count'   => Wishlist::where('user_id', $this->userId())->count(),
            ]);
        }

        return back()->with('success', '"' . $productName . '" removed from your wishlist.');
    }

    // -------------------------------------------------------------------------
    // Clear All
    // -------------------------------------------------------------------------

    /**
     * Clear the entire wishlist.
     */
    public function clear(): RedirectResponse
    {
        Wishlist::where('user_id', $this->userId())->delete();

        return back()->with('success', 'Your wishlist has been cleared.');
    }
}

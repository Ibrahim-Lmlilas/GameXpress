<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    const LOW_STOCK = 5;

    public function index()
    {
        $products = Product::with('category', 'images')->get();

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    public function show($id)
    {
        $product = Product::with('category', 'images')->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produit non trouvé'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:available,out_of_stock',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $product = Product::create($request->all());

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/product_images');
            $productImage = new ProductImage([
                'product_id' => $product->id,
                'image_url' => Storage::url($imagePath),
                'is_primary' => true,
            ]);
            $productImage->save();
        }

        return response()->json([
            'message' => 'Produit créé avec succès',
            'product' => $product
        ], 201);
    }

    public function update(Request $request, string $id)
    {

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }
        $validateur = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'status' => 'sometimes|required|in:available,out_of_stock',
            'category_id' => 'sometimes|required|exists:categories,id',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation for image
        ]);

        if($validateur->fails()){
            return response()->json(['message' => 'Validation failed', 'errors' => $validateur->errors()], 400);
        }


        $product->name = $request->input('name', $product->name);
        $product->slug = $request->input('slug', $product->slug);
        $product->price = $request->input('price', $product->price);
        $product->stock = $request->input('stock', $product->stock);
        $product->status = $request->input('status', $product->status);
        $product->category_id = $request->input('category_id', $product->category_id);
        $product->update($request->except('image'));
        $product->save();

        if ($request->hasFile('image')) {
            if ($product->images()->exists()) {
                $oldImage = $product->images()->first();
                if (Storage::disk('public')->exists(str_replace('/storage/', '', $oldImage->image_url))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $oldImage->image_url));
                }
                $oldImage->delete();
            }

            $imagePath = $request->file('image')->store('public/product_images');
            $productImage = new ProductImage([
                'product_id' => $product->id,
                'image_url' => Storage::url($imagePath),
                'is_primary' => true,
            ]);
            $productImage->save();
        }

        return response()->json([
            'message' => 'Produit mis à jour avec succès',
            'product' => $product->load('images')
        ], 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produit non trouvé'
            ], 404);
        }

        foreach ($product->images as $image) {
            if (Storage::disk('public')->exists(str_replace('/storage/', '', $image->image_url))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $image->image_url));
            }
            $image->delete();
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Produit supprimé avec succès'
        ]);
    }

    // private function checkLowStock(Product $product)
    // {
    //     if ($product->stock <= self::LOW_STOCK) {
    //         $admins = User::role(['super_admin', 'product_manager'])->get();

    //         foreach ($admins as $admin) {
    //             $admin->notify(new LowStockNotification($product));
    //         }

    //         return true;
    //     }

    //     return false;
    // }
}

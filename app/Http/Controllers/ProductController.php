<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::all();
        if($request->ajax()){
            return view('back.products.ajax-table',compact('products'))->render();
        }
        return view('back.products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|string',
            'description' => 'required|string',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
        ]);
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = md5(rand(1111, 9999) . microtime()) . '.' . $image->extension();
                $image->storeAs('public/products/', $path);
                $images[] = $path;
            }
        }

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'images' => json_encode($images),
        ]);

        // return response()->json(['success' => 'Product added successfully']);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|string',
            'description' => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $oldImages = json_decode($product->images, true);

    // Initialize an array to store new image paths
    $images = [];

    // Check if there are new images in the request
    if ($request->hasFile('images')) {
        // Loop through each image and store it
        foreach ($request->file('images') as $image) {
            $path = md5(rand(1111, 9999) . microtime()) . '.' . $image->extension();
            $image->storeAs('public/products/', $path);
            $images[] =  $path;
        }

        // Delete the old images from storage
        foreach ($oldImages as $oldImage) {
            Storage::delete('public/products/' . $oldImage);
        }
    } else {
        // If no new images are uploaded, keep the old ones
        $images = $oldImages;
    }
        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'images' => json_encode($images),
        ]);

        return response()->json(['success' => 'Product updated successfully']);
    }

    public function destroy(Product $product)
    {
        $images = json_decode($product->images, true);
        foreach ($images as $image) {
            Storage::delete('public/products/' . $image);
        }
        $product->delete();
        return response()->json(['success' => 'Product deleted successfully']);
    }
}

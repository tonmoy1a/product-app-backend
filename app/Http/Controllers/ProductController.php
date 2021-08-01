<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Str;
use File;

class ProductController extends Controller
{

    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $products = Product::latest()->get();

        return $products;
    }

    public function store(Request $request)
    {
        $product = new Product();

        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;

        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = time().'_'.Str::slug($image->getClientOriginalName());
            $image->storeAs('public/products', $imageName);
        }

        $product->image = $imageName;

        $product->save();
    }

    public function show($id)
    {
        $product = Product::find($id);

        if($product){
            return $product;
        }else{
            return response(["error" => "product not found"], 404);
        }
        
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;

        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = time().'_'.Str::slug($image->getClientOriginalName());
            $image->storeAs('public/products', $imageName);
        }else{
            $imageName = $product->image;
        }

        $product->image = $imageName;

        $product->save();
        
        return $product;
    }


    public function destroy($id)
    {
        $product = Product::find($id);

        if($product){
            $imagePath = storage_path('app/public/products/'.$product->image);
            if($imagePath){
                File::delete($imagePath);
            }
            $product->delete();
            return response(['success'=> 'success']);
        }

        return response(['error'=> 'Not Found'], 404);
    }
}

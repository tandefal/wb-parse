<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getProducts(Request $request)
    {
        $query = $request->query('query');//футболка,джинсы,платье

        $products = Product::select('name', 'price', 'image', 'brand_id')
            ->where('name', 'like', "%{$query}%")
            ->with(['brand' => function ($query) {
                $query->select('id','name');
            }])
            ->get();

        return response()->json($products);
    }
}

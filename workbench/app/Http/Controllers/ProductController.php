<?php

declare(strict_types=1);

namespace Workbench\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workbench\App\Http\Responses\CreateProduct;
use Workbench\App\Http\Responses\DeleteProduct;
use Workbench\App\Http\Responses\EditProduct;
use Workbench\App\Http\Responses\IndexProduct;
use Workbench\App\Http\Responses\ShowProduct;
use Workbench\App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return (new IndexProduct())
            ->title('Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $store = route('products.store');

        return (new CreateProduct($store))
            ->title('Create')
            ->page('Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return (new ShowProduct($product))
            ->title('Show')
            ->page('Show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $update = route('products.update', $product);

        return (new EditProduct($product, $update))
            ->title('Edit')
            ->page('Edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Show the confirmation page for deleting the specified resource.
     */
    public function delete(Product $product)
    {
        $destroy = route('products.destroy', $product);

        return (new DeleteProduct($product, $destroy))
            ->title('Delete')
            ->page('Delete');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}

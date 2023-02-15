<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::included()
            ->filter()
            ->sort()
            ->getOrPaginate();

        return CategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|max:255|unique:categories',
        ]);

        $category = Category::create($request->all());

        return CategoryResource::make($category);
    }


    public function show($id)
    {
        $category = Category::included()->findOrFail($id);
        return CategoryResource::make($category);
    }


    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|max:255|unique:categories,slug,' . $category->id,
        ]);

        $category->update($request->all());

        return CategoryResource::make($category);
    }


    public function destroy(Category $category)
    {
        $category->delete();
        return CategoryResource::make($category);
    }
}

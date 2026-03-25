<?php

namespace App\Http\Controllers\Api;

use App\Models\CarModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CarModelController extends Controller
{

    public function index()
    {
        return CarModel::with('brand')->paginate(10);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'brand_id' => 'required|exists:cars_brands,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|unique:cars_models',
            'year_start' => 'nullable|integer',
            'year_end' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        $model = CarModel::create($data);

        return response()->json($model,201);
    }

    public function show(CarModel $carModel)
    {
        return $carModel->load('brand','cars');
    }

    public function update(Request $request, CarModel $carModel)
    {
        $data = $request->validate([
            'brand_id' => 'sometimes|exists:cars_brands,id',
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:cars_models,slug,' . $carModel->id,
            'year_start' => 'nullable|integer',
            'year_end' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);
    
        $carModel->update($data);
    
        return response()->json($carModel);
    }
    public function destroy(CarModel $carModel)
    {
        $carModel->delete();

        return response()->json([
            'message' => 'Model deleted'
        ]);
    }

}
<?php

namespace App\Http\Controllers\Api;

use App\Models\CarBrand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ImageRepository;

class CarBrandController extends Controller
{
  
    public function index()
    {   
        return response()->json(CarBrand::paginate(10), 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'country' => 'nullable|string|max:100',
           'is_active' => 'boolean|nullable',
        ]);

        $data['is_active'] = filter_var($data['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $brand = CarBrand::create($data);

        return response()->json([
            'id' => $brand->id,
            'country' => $brand->country,
            'is_active' => $brand->is_active
        ], 201);
    }

    
    public function show(CarBrand $carBrand)
    {
        return response()->json($carBrand->load('models'), 200);
    }

    public function update(Request $request, CarBrand $carBrand)
    {
        $data = $request->validate([
            'country' => 'nullable|string|max:100',
            'is_active' => 'sometimes|boolean'
        ]);

        $carBrand->update($data);

        return response()->json([
            'id' => $carBrand->id,
            'country' => $carBrand->country,
            'is_active' => $carBrand->is_active
        ], 200);
    }

    public function destroy(CarBrand $carBrand, ImageRepository $imageRepo)
    {
      
        if ($carBrand->logo) {
            $imageRepo->delete($carBrand->logo);
        }
    
        $carBrand->delete();
    
        return response()->json([
            'message' => 'Brand deleted successfully'
        ], 200);
    }
    public function uploadLogo(Request $request, CarBrand $carBrand, ImageRepository $imageRepo)
{
    $request->validate([
        'logo' => ['required', 'image   ', 'max:2048']
    ]);

    $path = $imageRepo->replace(
        $request->file('logo'),
        $carBrand->logo,
        'brands'
    );

    $carBrand->update(['logo' => $path]);

    return response()->json([
        'message' => 'Logo uploaded',
        'brand' => $carBrand
    ]);
}
}
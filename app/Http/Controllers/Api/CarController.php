<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ChecksCompanyAccess;
use App\Repositories\ImageRepository;
use App\Models\Car;
use App\Models\CarImage;

class CarController extends Controller
{
    use ChecksCompanyAccess;

    public function index()
    {
        if (auth()->user()->role->name === 'admin') {
            return Car::with(['brand','model','images'])->paginate(10);
        }

        return Car::whereHas('company', function ($q) {
            $q->where('owner_id', auth()->id());
        })->with(['brand','model','images'])->paginate(10);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'brand_id' => 'required|exists:cars_brands,id',
            'model_id' => 'required|exists:cars_models,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'year' => 'required|integer',
            'color' => 'required|string',
            'fuel_type' => 'required|in:petrol,diesel,hybrid,electric',
            'transmission' => 'required|in:manual,automatic',
            'engine' => 'nullable|string',
            'horsepower' => 'nullable|integer',
            'mileage' => 'nullable|integer',
            'price' => 'required|numeric',
        ]);

        $this->checkCompanyAccess($data['company_id']);

        $car = Car::create($data);

        return response()->json($car, 201);
    }

    public function show(Car $car)
    {
        $this->checkCompanyAccess($car->company_id);

        return $car->load('brand','model','images');
    }

    public function update(Request $request, Car $car)
    {
        $this->checkCompanyAccess($car->company_id);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'year' => 'sometimes|integer',
            'color' => 'sometimes|string',
            'fuel_type' => 'sometimes|in:petrol,diesel,hybrid,electric',
            'transmission' => 'sometimes|in:manual,automatic',
            'engine' => 'nullable|string',
            'horsepower' => 'nullable|integer',
            'mileage' => 'nullable|integer',
            'price' => 'sometimes|numeric',
        ]);

        $car->update($data);

        return response()->json($car);
    }

    public function destroy(Car $car, ImageRepository $imageRepo)
    {
        $this->checkCompanyAccess($car->company_id);

        if ($car->main_image) {
            $imageRepo->delete($car->main_image);
        }

        foreach ($car->images as $image) {
            $imageRepo->delete($image->url);
            $image->delete();
        }

        $car->delete();

        return response()->json([
            'message' => 'Car deleted successfully'
        ]);
    }

    public function uploadMainImage(Request $request, Car $car, ImageRepository $imageRepo)
    {
        $this->checkCompanyAccess($car->company_id);

        $request->validate([
            'image' => ['required', 'image']
        ]);

        $path = $imageRepo->replace($request->file('image'), $car->main_image, 'cars');

        $car->update(['main_image' => $path]);

        $car->images()->create([
            'url' => $path,
            'type' => 'main'
        ]);

        return response()->json([
            'message' => 'Main image updated',
            'car' => $car
        ]);
    }

    public function uploadImages(Request $request, Car $car, ImageRepository $imageRepo)
    {
        $this->checkCompanyAccess($car->company_id);

        $request->validate([
            'images.*' => ['required', 'image']
        ]);

        foreach ($request->file('images') as $file) {
            $path = $imageRepo->upload($file, 'cars');

            $car->images()->create([
                'url' => $path,
                'type' => 'gallery'
            ]);
        }

        return response()->json([
            'message' => 'Images uploaded'
        ]);
    }

    public function deleteImage(CarImage $image, ImageRepository $imageRepo)
    {
        $this->checkCompanyAccess($image->car->company_id);

        $imageRepo->delete($image->url);
        $image->delete();

        return response()->json([
            'message' => 'Image deleted'
        ]);
    }

    public function deleteMainImage(Car $car, ImageRepository $imageRepo)
    {
        $this->checkCompanyAccess($car->company_id);

        $imageRepo->delete($car->main_image);

        $car->update(['main_image' => null]);

        return response()->json([
            'message' => 'Main image deleted'
        ]);
    }
}
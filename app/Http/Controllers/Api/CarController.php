<?php



namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\ImageRepository; // ✅ kjo është e saktë
use App\Models\Car;
use App\Models\CarImage;
class CarController extends Controller
{

    public function index()
    {
        return Car::with(['brand','model','images'])->paginate(10);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
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
            'currency' => 'nullable|string',
            'status' => 'nullable|in:available,sold,reserved',
            'featured' => 'boolean',
            'main_image' => 'nullable|string'
        ]);
    
        $car = Car::create($data);
    
        return response()->json($car, 201);
    }

    public function show(Car $car)
    {
        return $car->load('brand','model','images');
    }

    public function update(Request $request, Car $car)
{
    $data = $request->validate([
        'brand_id' => 'sometimes|exists:cars_brands,id',
        'model_id' => 'sometimes|exists:cars_models,id',
        'title' => 'sometimes|string|max:255',
        'description' => 'nullable|string',
        'year' => 'sometimes|integer',
        'color' => 'sometimes|string',
        'fuel_type' => 'sometimes|in:petrol,diesel,hybrid,electric',
        'transmission' => 'sometimes|in:manual,automatic',
        'engine' => 'nullable|string',
        'horsepower' => 'nullable|integer',
        'mileage' => 'nullable|string',
        'price' => 'sometimes|numeric',
        'currency' => 'nullable|string',
        'status' => 'nullable|in:available,sold,reserved',
        'featured' => 'boolean',
        'main_image' => 'nullable|string'
    ]);

    $car->update($data);

    return response()->json($car);
}

    public function destroy(Car $car)
    {
        $car->delete();

        return response()->json([
            'message' => 'Car deleted'
        ]);
    }


    public function uploadMainImage(Request $request, Car $car, ImageRepository $imageRepo)
    {
        $request->validate([
            'image' => ['required', 'image']
        ]);
    
        $path = $imageRepo->replace($request->file('image'), $car->main_image, 'cars');
    
        $car->update(['main_image' => $path]);
    
        return response()->json([
            'message' => 'Main image updated',
            'car' => $car
        ]);
 
        

    }




    public function uploadImages(Request $request, Car $car, ImageRepository $imageRepo)
    {
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
    $imageRepo->delete($image->url);

    $image->delete();

    return response()->json([
        'message' => 'Image deleted'
    ]);

    
}



public function deleteMainImage(Car $car, ImageRepository $imageRepo)
{
    $imageRepo->delete($car->main_image);

    $car->update(['main_image' => null]);

    return response()->json([
        'message' => 'Main image deleted'
    ]);
}
}
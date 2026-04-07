<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ImageRepository;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role->name === 'admin') {
            return Company::paginate(10);
        }

        return Company::where('owner_id', $user->id)->paginate(10);
    }

    public function store(Request $request, ImageRepository $imageRepo)
    {

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'business_number' => 'nullable|string',
            'vat_number' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data['owner_id'] = $request->user()->id;

        
        if ($request->hasFile('logo')) {
            $data['logo'] = $imageRepo->upload(
                $request->file('logo'),
                'companies'
            );
        }

        $company = Company::create($data);

        return response()->json([
            'message' => 'Company created',
            'company' => $company
        ], 201);
    }

    public function show(Company $company)
    {
        if (
            auth()->user()->role->name !== 'admin' &&
            $company->owner_id !== auth()->id()
        ) {
            abort(403, 'You are not allowed to view this company');
        }

        return $company;
    }

    public function update(Request $request, Company $company, ImageRepository $imageRepo)
    {
        if (
            $company->owner_id !== $request->user()->id &&
            auth()->user()->role->name !== 'admin'
        ) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'business_number' => 'nullable|string',
            'vat_number' => 'nullable|string',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|max:2048',
        ]);

        
        if ($request->hasFile('logo')) {
            $data['logo'] = $imageRepo->replace(
                $request->file('logo'),
                $company->logo,
                'companies'
            );
        }

        $company->update($data);

        return response()->json([
            'message' => 'Company updated',
            'company' => $company
        ]);
    }

    public function destroy(Request $request, Company $company)
    {
        if ($company->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $company->delete();

        return response()->json([
            'message' => 'Company deleted'
        ]);
    }

    public function uploadLogo(Request $request, Company $company, ImageRepository $imageRepo)
    {
        
        if (
            $company->owner_id !== auth()->id() &&
            auth()->user()->role->name !== 'admin'
        ) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'logo' => ['required', 'image', 'max:2048']
        ]);

        $path = $imageRepo->replace(
            $request->file('logo'),
            $company->logo,
            'companies'
        );

        $company->update(['logo' => $path]);

        return response()->json([
            'message' => 'Logo uploaded',
            'company' => $company
        ]);
    }
}
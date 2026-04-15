<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();

        return Client::where('company_id', $user->company_id)
            ->latest()
            ->paginate(10);
    }


    public function store(Request $request)
    {
        $user = Auth::user();


        // if (!$user || !$user->company_id) {
        //     return response()->json([
        //         'message' => 'User has no company assigned'
        //     ], 403);
        // }
    
        $data = $request->validate([
            'client_type' => 'required|in:business,private',
    
            'business_name' => 'required_if:client_type,business|string|max:255',
            'additional_company_name' => 'nullable|string|max:255',
    
            'first_name' => 'required_if:client_type,private|string|max:255',
            'last_name' => 'required_if:client_type,private|string|max:255',
    
            'street' => 'nullable|string',
            'building_number' => 'nullable|string',
            'additional_address_info' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
    
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'mobile' => 'nullable|string',

            'company_id' => 'nullable|integer',
    
            'client_language' => 'nullable|string',
            'remarks' => 'nullable|string',
            'category' => 'nullable|string',
            'employees_count' => 'nullable|integer',
        ]);
    
      
        $data['company_id'] = $data['company_id'];
        $data['client_type'] = $data['client_type'];
    

        if ($data['client_type'] === 'business') {
            $data['first_name'] = null;
            $data['last_name'] = null;
        } else {
            $data['business_name'] = null;
            $data['additional_company_name'] = null;
        }
    
        $client = Client::create($data);
    
        return response()->json([
            'message' => 'Client created',
            'client' => $client
        ], 201);
    }

   
    public function show(Request $request, Client $client)
    {
        $this->authorizeClient($request, $client);

        return $client;
    }

    
    public function update(Request $request, Client $client)
    {
        $this->authorizeClient($request, $client);

        $data = $request->validate([
            'client_type' => 'nullable|in:business,private',

            'business_name' => 'nullable|string|max:255',
            'additional_company_name' => 'nullable|string|max:255',

            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',

            'street' => 'nullable|string',
            'building_number' => 'nullable|string',
            'additional_address_info' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',

            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'mobile' => 'nullable|string',

            'client_language' => 'nullable|string',
            'remarks' => 'nullable|string',
            'category' => 'nullable|string',
            'employees_count' => 'nullable|integer',
        ]);

        

        $client->update($data);

        return response()->json([
            'message' => 'Client updated',
            'client' => $client
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
    
        $client = Client::find($id);
    
        if (!$client) {
            return response()->json([
                'message' => 'Client not found'
            ], 404);
        }
    
        if ($client->company_id !== $user->company_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
    
        $client->delete();
    
        return response()->json([
            'message' => 'Client deleted'
        ]);
    }

    private function authorizeClient(Request $request, Client $client)
    {
        $user = Auth::user();
    
        if (!$user || $client->company_id !== $user->company_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
    }
}
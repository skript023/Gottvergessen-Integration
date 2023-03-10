<?php

namespace App\Http\Controllers;

use App\Models\owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SignedInValidation extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Store access
    |--------------------------------------------------------------------------
    |
    | Loader will store credentials to Big Base Integration before library injection.
    | Since loader cannot store data directly to Shared Library.
    | 
    |
    */
    public function store_access(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'hardware_uuid' => 'required',
            'computer_name' => 'required',
            'role',
            'token' => 'required'
        ]);

        $data = $request->only([
            'username',
            'hardware_uuid',
            'computer_name',
            'role',
            'token'
        ]);

        $injection_code = Jenkins::hash($request->username . '_' . $request->hardware_uuid);

        $data['injection'] = $injection_code;

        try 
        {
            owner::updateOrCreate([
                'username' => $request->username,
            ], $data);

            return response()->json([
                'message' => 'Integration success, attempting DLL injection'
            ]);
        } 
        catch (\Throwable $th) 
        {
            return response()->json([
                'message' => 'Error : '.$th
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Big Base initialization
    |--------------------------------------------------------------------------
    |
    | Shared Library will attempting to call API to get credentials data.
    | After credentials acquired, Big Base services will be activated.
    | 
    |
    */
    public function allow_injection(Request $request)
    {
        $user = owner::where('hardware_uuid', $request->hardware)->first();

        if (is_null($user)) return response()->json(['message' => 'Injection does not allowed, potential illegal access detected.']);

        return response()->json([
            'message' => 'Injection success, enjoy your game.',
            'data' => $user
        ]);
    }

    public function delete_old(Request $request)
    {
        $stale_posts = owner::where('created_at', '<', now()->subDay())->get();
        foreach ($stale_posts as $post) 
        {
            $post->delete();
        }

        return response()->json($stale_posts);
    }
}

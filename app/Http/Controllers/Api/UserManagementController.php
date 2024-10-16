<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Get detail on current user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentUser()
    {
        $aAuthUser = Auth::user();
        return response()->json([
            'result' => 'success',
            'data'   => $aAuthUser
        ]);
    }
}

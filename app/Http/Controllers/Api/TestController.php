<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testRegister(Request $request)
    {
        return response()->json([
            'message' => 'El TestController funciona perfectamente!',
            'data_received' => $request->all()
        ]);
    }
}
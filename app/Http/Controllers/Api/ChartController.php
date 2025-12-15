<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DealStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function pipeline()
    {
        $user = Auth::user();

        // Obtenemos las etapas y calculamos el valor de los deals abiertos para cada una
        $data = DealStage::orderBy('order')
            ->withSum(['deals' => function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('status', 'open');
            }], 'value')
            ->get();
        
        // Formateamos los datos para ApexCharts
        $chartData = [
            'labels' => $data->pluck('name'),
            'series' => $data->pluck('deals_sum_value')->map(fn ($value) => $value ?? 0),
        ];

        return response()->json($chartData);
    }
}
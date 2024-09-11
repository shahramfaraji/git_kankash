<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use Illuminate\Support\Facades\Auth;

class CityController extends Controller
{
    public function index()
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $city = City::all();
        return response()->json(['city' => $city], 200);
    }

    public function show($id)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $city = City::find($id);

        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }
    
        return response()->json(['city' => $city], 200);
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'KM' => 'required|numeric',
        ]);

        $city = City::create([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'KM' => $request->input('KM')
        ]);

        return response()->json(['city' => $city], 201);
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'KM' => 'required|numeric'
        ]);
        
        $city = City::find($id);

        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }
        
        $city->update([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'KM' => $request->input('KM')
        ]);
        
        return response()->json(['city' => $city], 200);
    }

    public function destroy($id)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $city = City::find($id);

        if ($city) {
            $city->delete();

            return response()->json(['message' => 'حذف با موفقیت انجام شد'], 200);
        } else {
            return response()->json(['message' => 'شهر پیدا نشد'], 404);
        }
    }
}
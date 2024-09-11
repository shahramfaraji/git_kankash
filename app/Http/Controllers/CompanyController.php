<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    
    public function index()
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $com = Company::all();
        return response()->json(['com' => $com], 200);
    }

    public function show($id)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $com = Company::find($id);

        if (!$com) {
            return response()->json(['message' => 'Company not found'], 404);
        }
    
        return response()->json(['com' => $com], 200);
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
            'name' => 'required|string'
        ]);

        $com = Company::create([
            'name' => $request->input('name'),
        ]);

        return response()->json(['com' => $com], 201);
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
        ]);
        
        $com = Company::find($id);

        if (!$com) {
            return response()->json(['message' => 'Company not found'], 404);
        }
        
        $com->update([
            'name' => $request->input('name'),
        ]);
        
        return response()->json(['com' => $com], 200);
    }

    public function destroy($id)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        
        $com = Company::find($id);

        if ($com) {
            $com->delete();

            return response()->json(['message' => 'حذف با موفقیت انجام شد'], 200);
        } else {
            return response()->json(['message' => 'سازمان پیدا نشد'], 404);
        }
    }
}
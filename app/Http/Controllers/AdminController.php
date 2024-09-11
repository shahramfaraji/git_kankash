<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role == 0 ) {
            return response()->json(['error' => ''], 404);
        }
        $users = User::all();
        $count_admin = User::where('role',1)->get();
        return response()->json(['users' => $users, 'count_admin' => count($count_admin)], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $request->validate([
            'role' => 'numeric',
            'user' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'role' => $request->input('role'),
            'user' => $request->input('user'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        return response()->json(['user' => $user], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        return response()->json(['user' => $user], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $request->validate([
            'role' => 'numeric',
            'user' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'required|min:6',
        ]);
        
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        $user->update([
            'role' => $request->input('role'),
            'user' => $request->input('user'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
        
        return response()->json(['user' => $user], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $user = User::find($id);
        if($user->role == 1) {
            $user_count = User::where('role',1)->get();
            if(count($user_count)== 1) {
                return response()->json(['message' => 'تنها یک مدیر وجود دارد و نمیتواند حذف شود'], 404);
            }
        }    
        $services = Service::where('user_id', $id)->get();
        if ($user) {
            
            $services->each->delete();
            $user->delete();

            return response()->json(['message' => 'حذف با موفقیت انجام شد'], 200);
        } else {
            return response()->json(['message' => 'یوزر پیدا نشد'], 404);
        }
    }
}
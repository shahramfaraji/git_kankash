<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceResource;
use App\Http\Resources\UserResource;
use App\Models\Along;
use App\Models\City;
use App\Models\Company;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use League\OAuth1\Client\Server\Server;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::where('id', '!=', auth()->id())->get();
        $users = UserResource::collection($user);
        $city = City::all();
        $company = Company::all();
        $service = Service::whereHas('city')->whereHas('user')->with('city')->with('company')->with('user')->with('along')->where('user_id', auth()->id())->orderBy('created_at', 'desc')->limit(100)->get();
        return response()->json(['user' => $users, 'city' => $city, 'company' => $company, 'service' => $service], 200);
    }
    
    public function index_admin($id)
    {
        if(Auth::user()->role == 0) {
            return response()->json(['error' => ''], 404);
        }
        $user = User::where('id', '!=', $id)->get();
        $users = UserResource::collection($user);
        $city = City::all();
        $company = Company::all();
        $service = Service::whereHas('city')->whereHas('user')->with('city')->with('company')->with('user')->with('along')->where('user_id', $id)->orderBy('date', 'desc')->limit(100)->get();
        return response()->json(['user' => $users, 'city' => $city, 'company' => $company, 'service' => $service], 200);
    }

    public function index_admin_today($id)
    {
        if(Auth::user()->role == 0) {
            return response()->json(['error' => ''], 404);
        }
        $user = User::where('id', '!=', $id)->get();
        $users = UserResource::collection($user);
        $city = City::all();
        $company = Company::all();
        $service = Service::whereHas('city')->whereHas('user')->with('city')->with('company')->with('user')->with('along')->where('user_id', $id)->where('pay', null)->orWhere(
            function($query) use($id) {
                $query->where('pay_date', date('Y-m-d'))
                ->where('user_id', $id);
            }
        )->orderBy('date', 'desc')->get();
        return response()->json(['user' => $users, 'city' => $city, 'company' => $company, 'service' => $service], 200);
    }
    
    public function index_admin_all_service()
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $today = date('Y-m-d');
        $date100DaysAgo = date('Y-m-d', strtotime('-100 days'));

        $user_along = User::all();
        $users_along = UserResource::collection($user_along);
        $user = User::all();
        $city = City::all();
        $company = Company::all();
        $service = Service::whereHas('city')->whereHas('user')->with('city')->with('company')->with('user')->with('along')->where('date', '>=', $date100DaysAgo)->orderBy('date', 'desc')->get();
        $services_amin = ServiceResource::collection($service);

        return response()->json(['users_along' => $users_along, 'user' => $user, 'city' => $city, 'company' => $company, 'services' => $services_amin, 'service_all' => $service], 200);
    }
    
    public function index_admin_event_service($id)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $user_along = User::all();
        $users_along = UserResource::collection($user_along);
        $service = Service::whereHas('city')->whereHas('user')->with('city')->with('company')->with('user')->with('along')->where('id', $id)->get();

        return response()->json(['users_along' => $users_along, 'service' => $service[0]], 200);
    }
    
    public function servic_edit_admin($id)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $city = City::all();
        $company = Company::all();
        $service = Service::with('along')->find($id);
        $user = User::where('id', '!=', $service->user_id)->get();
        $users = UserResource::collection($user);
        return response()->json(['user' => $users, 'city' => $city, 'company' => $company, 'service' => $service], 200);
    }
    
    public function filter_index(Request $request)
    {
        // 'start' => 'required|date',
        $request->validate([
            'start' => 'required',
            'end' => 'required'
        ]);

        $jalaliDate_start = str_replace('/', '-', $request->input('start'));;
        $gregorianDate_start = Jalalian::fromFormat('Y-m-d', $jalaliDate_start)->toCarbon()->toDateString();
        $jalaliDate_end = str_replace('/', '-', $request->input('end'));;
        $gregorianDate_end= Jalalian::fromFormat('Y-m-d', $jalaliDate_end)->toCarbon()->toDateString();

        $user = User::where('id', '!=', auth()->id())->get();
        $users = UserResource::collection($user);
        $city = City::all();
        $company = Company::all();
        $service = Service::whereHas('city')->whereHas('user')->with('city')->with('company')->with('user')->with('along')->where('user_id', auth()->id())->whereBetween('date', [$gregorianDate_start, $gregorianDate_end])->orderBy('date', 'desc')->get();
        return response()->json(['user' => $users, 'city' => $city, 'company' => $company, 'service' => $service], 200);
    }

    public function filter_index_all(Request $request)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $request->validate([
            'start' => 'required',
            'end' => 'required'
        ]);

        $jalaliDate_start = str_replace('/', '-', $request->input('start'));;
        $gregorianDate_start = Jalalian::fromFormat('Y-m-d', $jalaliDate_start)->toCarbon()->toDateString();
        $jalaliDate_end = str_replace('/', '-', $request->input('end'));;
        $gregorianDate_end= Jalalian::fromFormat('Y-m-d', $jalaliDate_end)->toCarbon()->toDateString();

        $user_along = User::all();
        $users_along = UserResource::collection($user_along);
        $user = User::all();
        $city = City::all();
        $company = Company::all();
        if($request->input('id') != null) {
            if($request->input('price') != null) {
                $service = Service::whereHas('city')->whereHas('user')->with('city')->with('company')->with('user')->with('along')->where('user_id', $request->input('id'))->where('price', null)->whereBetween('date', [$gregorianDate_start, $gregorianDate_end])->orderBy('date', 'desc')->get();
            }
            else {
                $service = Service::whereHas('city')->whereHas('user')->with('city')->with('company')->with('user')->with('along')->where('user_id', $request->input('id'))->whereBetween('date', [$gregorianDate_start, $gregorianDate_end])->orderBy('date', 'desc')->get();
            }
        }
        else {
            if($request->input('price') != null) {
                $service = Service::whereHas('city')->whereHas('user')->with('city')->with('company')->with('user')->with('along')->where('price', null)->whereBetween('date', [$gregorianDate_start, $gregorianDate_end])->orderBy('date', 'desc')->get();
            }
            else {
                $service = Service::whereHas('city')->whereHas('user')->with('city')->with('company')->with('user')->with('along')->whereBetween('date', [$gregorianDate_start, $gregorianDate_end])->orderBy('date', 'desc')->get();
            }
        }
        $services_amin = ServiceResource::collection($service);

        return response()->json(['users_along' => $users_along, 'user' => $user, 'city' => $city, 'company' => $company, 'services' => $services_amin, 'service_all' => $service], 200);
    }

    public function filter_index_admin(Request $request)
    {
        if(Auth::user()->role == 0) {
            return response()->json(['error' => ''], 404);
        }
        if($request->input('start') != null) {
            $request->validate([
                'start' => 'required',
                'end' => 'required'
            ]);
    
            $jalaliDate_start = str_replace('/', '-', $request->input('start'));;
            $gregorianDate_start = Jalalian::fromFormat('Y-m-d', $jalaliDate_start)->toCarbon()->toDateString();
            $jalaliDate_end = str_replace('/', '-', $request->input('end'));;
            $gregorianDate_end= Jalalian::fromFormat('Y-m-d', $jalaliDate_end)->toCarbon()->toDateString();
    
            $user = User::where('id', '!=', $request->input('id'))->get();
            $users = UserResource::collection($user);
            $city = City::all();
            $company = Company::all();
            if($request->input('price') != null) {
                $service = Service::whereHas('city')->whereHas('user')->with('city')->with('company')->with('user')->with('along')->where('user_id', $request->input('id'))->where('pay', null)->whereBetween('date', [$gregorianDate_start, $gregorianDate_end])->orderBy('created_at', 'desc')->get();
            }
            else {
                $service = Service::whereHas('city')->whereHas('user')->with('city')->with('company')->with('user')->with('along')->where('user_id', $request->input('id'))->whereBetween('date', [$gregorianDate_start, $gregorianDate_end])->orderBy('created_at', 'desc')->get();
            }
        } else {
            $user = User::where('id', '!=', $request->input('id'))->get();
            $users = UserResource::collection($user);
            $city = City::all();
            $company = Company::all();
            if($request->input('price') != null) {
                $service = Service::whereHas('city')->whereHas('user')->with('city')->with('company')->with('user')->with('along')->where('user_id', $request->input('id'))->where('pay', null)->orderBy('created_at', 'desc')->get();
            }
            else {
                $service = Service::whereHas('city')->whereHas('user')->with('city')->with('company')->with('user')->with('along')->where('user_id', $request->input('id'))->orderBy('created_at', 'desc')->get();
            }
        }
        
        return response()->json(['user' => $users, 'city' => $city, 'company' => $company, 'service' => $service], 200);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pay_service(Request $request)
    {
        if(Auth::user()->role == 0) {
            return response()->json(['error' => ''], 404);
        }
        for($i= 0; $i < count($request->pay) ; $i++) {
            $service = Service::find($request->pay[$i]);
            $service->update([
                'pay' => 1,
                'pay_date' => date('Y-m-d')
            ]);
        }
        return response()->json(['service' => $request->pay], 201);

    }
    public function notpay_service(Request $request)
    {
        if(Auth::user()->role == 0) {
            return response()->json(['error' => ''], 404);
        }
        for($i= 0; $i < count($request->pay) ; $i++) {
            $service = Service::find($request->pay[$i]);
            $service->update([
                'pay' => null
            ]);
        }
        return response()->json(['service' => $request->pay], 201);

    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::user()->role == 3 ) {
            return response()->json(['error' => ''], 404);
        }
        $request->validate([
            'date' => 'required',
            'city' => 'required|integer'
        ]);
        // $new_date = Jalalian::fromFormat('Y-m-d', $request->input('date'))->toCarbon()->toDateString();
        $jalaliDate = str_replace('/', '-', $request->input('date'));;
        $gregorianDate = Jalalian::fromFormat('Y-m-d', $jalaliDate)->toCarbon()->toDateString();
        $service = Service::create([
            'user_id' => auth()->id(),
            'date' => $gregorianDate,
            'city_id' => $request->input('city'),
            'company_id' => $request->input('company'),
            'more' => $request->input('more')
        ]);

        if(count($request->input('alongs'))>0){
            for ($i = 0; $i < count($request->input('alongs')); $i++) {
                Along::create([
                    'service_id'=> $service->id,
                    'along_id' => intval($request->input('alongs')[$i]),
                ]);
            }
        }

        return response()->json(['service' => $service], 201);
    }
    

    public function store_admin(Request $request)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }
        $request->validate([
            'id' => 'required|integer',
            'date' => 'required',
            'city' => 'required|integer'
        ]);
        // $new_date = Jalalian::fromFormat('Y-m-d', $request->input('date'))->toCarbon()->toDateString();
        $jalaliDate = str_replace('/', '-', $request->input('date'));;
        $gregorianDate = Jalalian::fromFormat('Y-m-d', $jalaliDate)->toCarbon()->toDateString();
        $service = Service::create([
            'user_id' => $request->input('id'),
            'date' => $gregorianDate,
            'city_id' => $request->input('city'),
            'company_id' => $request->input('company'),
            'more' => $request->input('more'),
            'price' => $request->input('price')
        ]);

        if(count($request->input('alongs'))>0){
            for ($i = 0; $i < count($request->input('alongs')); $i++) {
                Along::create([
                    'service_id'=> $service->id,
                    'along_id' => intval($request->input('alongs')[$i]),
                ]);
            }
        }
        
        return response()->json(['service' => $service], 201);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    
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
            'date' => 'required',
            'city' => 'required|integer'
        ]);
        
        
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'service not found'], 404);
        }
        
        $jalaliDate = str_replace('/', '-', $request->input('date'));
        $gregorianDate = Jalalian::fromFormat('Y-m-d', $jalaliDate)->toCarbon()->toDateString();
        $service->update([
            'date' => $gregorianDate,
            'city_id' => $request->input('city'),
            'company_id' => $request->input('company'),
            'more' => $request->input('more'),
            'price' => $request->input('price')
        ]);

        Along::where('service_id', $id)->delete();
        if(count($request->input('alongs'))>0){
            for ($i = 0; $i < count($request->input('alongs')); $i++) {
                Along::create([
                    'service_id'=> $id,
                    'along_id' => intval($request->input('alongs')[$i]),
                ]);
            }
        }
        
        return response()->json(['service' => $service], 200);
    }
    
    public function update_price(Request $request, $id)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }

        $request->validate([
            'price' => 'required|numeric',
        ]);
        
        
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'service not found'], 404);
        }
        
        $service->update([
            'price' => $request->input('price')
        ]);
        
        return response()->json(['service' => $service], 200);
    }

    public function update_mission_price(Request $request, $id)
    {
        if(Auth::user()->role != 1) {
            return response()->json(['error' => ''], 404);
        }

        $request->validate([
            'price' => 'required|numeric',
        ]);
        
        
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'service not found'], 404);
        }
        
        $service->update([
            'mission_price' => $request->input('price')
        ]);
        
        return response()->json(['service' => $service], 200);
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
        $service = Service::find($id);
        Along::where('service_id', $id)->delete();
        if ($service) {
            $service->delete();

            return response()->json(['message' => 'حذف با موفقیت انجام شد'], 200);
        } else {
            return response()->json(['message' => 'سرویس پیدا نشد'], 404);
        }
    }
}
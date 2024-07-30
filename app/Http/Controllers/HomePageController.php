<?php

namespace App\Http\Controllers;

use App\Models\UserWater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomePageController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }
    public function getContact(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'phone'=>'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>403,
                'message'=>$validator->errors()->first(),
            ]);
        }
        UserWater::create([
            'name'=>$request->name,
            'phone'=>$request->phone,
            'comment'=>$request->comment,
            'social'=>'Veb sayt',
        ]);
        return response()->json([
            'status'=>200,
            'message'=>'Ma\'lumotlar saqlandi. Sizga tez orada adminlar aloqaga chiqishadi!'
        ]);
    }
}

<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use WebToPay;
use WebToPayException;
use App\Models\Doctor;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home.index');
    }

    public function searching(Request $request)
    {
        $search = $request->input('text');
    
        $filter = DB::table('doctors')
            ->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('description', 'LIKE', '%' . $search . '%')
            ->get();

        $cn = 0;
        foreach($filter as $tp){
            $specialisties = DB::table('doctor_specialities')
                ->where('doctor_id', $tp->id)->get();
            $cnt = 0;
            $rlt = "";
            foreach($specialisties as $tp){
                $specialist[$cnt] = DB::table('specialities')
                    ->where('id', $tp->speciality_id)->get();
                $rlt = $rlt.' '.$specialist[$cnt][0]->name;
                $cnt ++;
            }
            $filter[$cn]->specialist = $rlt;
            $cn ++;
        }

        $cn = 0;
        foreach($filter as $tp){
            $addresses = DB::table('addresses')
                ->where('user_id', $tp->user_id)->get();
            $cnt = 0;
            $rlt = [];
            foreach($addresses as $tp){
                $rlt[$cnt] = $tp->description.':'.$tp->address;
                $cnt ++;
            }
            $filter[$cn]->address = $rlt;
            $cn ++;
        }

        $cn = 0;
        foreach($filter as $tp){
            $experiences = DB::table('experiences')
                ->where('doctor_id', $tp->id)->get();
            $cnt = 0;
            $rlt = [];
            foreach($experiences as $tp){
                $rlt[$cnt] = $tp->title.':'.$tp->description;
                $cnt ++;
            }
            $filter[$cn]->experience = $rlt;
            $cn ++;
        }
        $cn = 0;
        foreach($filter as $tp){
            $reviews = DB::table('doctor_reviews')
                ->where('doctor_id', $tp->id)->get();
            $cnt = 0;
            $rlt = [];
            foreach($reviews as $tp){
                $rlt[$cnt] = $tp->review;
                $cnt ++;
            }
            $filter[$cn]->review = $rlt;
            $cn ++;
        }
        $cn = 0;
        foreach($filter as $tp){
            $hours = DB::table('availability_hours')
                ->where('doctor_id', $tp->id)->latest()->first();
            $cnt = 0;
            $rlt = [];
            foreach($hours as $tp){
                $rlt[$cnt] = $tp->review;
                $cnt ++;
            }
            $filter[$cn]->review = $rlt;
            $cn ++;
        }
        dd($filter);
        return view('home.filter')->with('doctors', $filter);
    }




}
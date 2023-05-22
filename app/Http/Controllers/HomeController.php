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
    public function index(Request $request)
    {
        $promise = DB::table('appointments')
            ->where('user_id', $request->id)->get();
            $cnt = 0;
            $cn = 0;
            $promisetp = [];
        foreach($promise as $tp){
            $path = DB::table('media')->where('model_type', 'App\Models\Doctor')->where('model_id', json_decode($tp->doctor)->id)->where('collection_name', 'avatar')->first();
            if(!$path){
                $path = DB::table('media')->where('model_type', 'App\Models\Doctor')->where('model_id', json_decode($tp->doctor)->id)->where('collection_name', 'image')->first();
            }
            if($path){
                $path = "storage/app/public/".$path->id."/conversions/".$path->name."-icon.jpg";
            }
            $promise[$cnt]->userimagepath = $path;
            if(!$tp->cancel){
                $promisetp[$cn++] = $promise[$cnt];
            }
            $cnt++;
        }
        $promise = $promisetp;
        return view('home.index')->with('promise', $promise);
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
                $rlt[$cnt] = $tp->description.': '.$tp->address;
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
            $rlt[0] = DB::table('availability_hours')
                ->where('doctor_id', $tp->id)->where('day', 'monday')->latest('id')->first();
            $rlt[1] = DB::table('availability_hours')
                ->where('doctor_id', $tp->id)->where('day', 'tuesday')->latest('id')->first();
            $rlt[2] = DB::table('availability_hours')
                ->where('doctor_id', $tp->id)->where('day', 'wednesday')->latest('id')->first();
            $rlt[3] = DB::table('availability_hours')
                ->where('doctor_id', $tp->id)->where('day', 'thursday')->latest('id')->first();
            $rlt[4] = DB::table('availability_hours')
                ->where('doctor_id', $tp->id)->where('day', 'friday')->latest('id')->first();
            $rlt[5] = DB::table('availability_hours')
                ->where('doctor_id', $tp->id)->where('day', 'saturday')->latest('id')->first();
            $rlt[6] = DB::table('availability_hours')
                ->where('doctor_id', $tp->id)->where('day', 'sunday')->latest('id')->first();

            $filter[$cn]->hour = $rlt;
            $cn ++;
        }

        $cnt = 0;
        foreach($filter as $tp){
            $path = DB::table('media')->where('model_type', 'App\Models\Doctor')->where('model_id', $tp->id)->where('collection_name', 'avatar')->first();
            if(!$path){
                $path = DB::table('media')->where('model_type', 'App\Models\Doctor')->where('model_id', $tp->id)->where('collection_name', 'image')->first();
            }
            if($path){
                $path = "storage/app/public/".$path->id."/conversions/".$path->name."-icon.jpg";
            }
            $filter[$cnt]->userimagepath = $path;
            $cnt++;
        }

        return view('home.filter')->with('doctors', $filter);
    }

    public function promisedelete(Request $request){
        DB::table('appointments')->where('id', $request->id)
                                    ->update(array('cancel' => '1'));
        return back();
    }

    public function openbooking(Request $request){
        $hour = DB::table('availability_hours')
                ->where('doctor_id', $request->doctor_id)
                ->where('day', $request->day)->latest('id')->first();

        $patients = DB::table('patients')->where('user_id', auth()->user()->id)->get();
        $user_id_doc = DB::table('doctors')->where('id', $request->doctor_id)->first()->user_id;
        $doctor_address = DB::table('addresses')->where('user_id', $user_id_doc)->get();
        $patient_address = DB::table('addresses')->where('user_id', auth()->user()->id)->get();
        $path = DB::table('media')->where('model_type', 'App\Models\Doctor')->where('model_id', $request->doctor_id)->where('collection_name', 'avatar')->first();
        if(!$path){
            $path = DB::table('media')->where('model_type', 'App\Models\Doctor')->where('model_id', $request->doctor_id)->where('collection_name', 'image')->first();
        }
        if($path){
            $path = "storage/app/public/".$path->id."/conversions/".$path->name."-icon.jpg";
        }
        $doctor = DB::table('doctors')->where('id', $request->doctor_id)->first();

        if($hour)return response()->json([
            'status' => 'success',
            'doctor' => $doctor,
            'patients' => $patients,
            'patient_address' => $patient_address,
            'doctor_address' => $doctor_address,
            'doctor_image' => $path,
            'start' => $hour->start_at,
            'end' => $hour->end_at
        ]);
        else return response()->json(['status'=>'failed']);
    }

    function booknow(Request $request){
        
    }

}
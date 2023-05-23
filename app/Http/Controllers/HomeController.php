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
            ->where('user_id', $request->id)->orderby('created_at', 'DESC')->get();
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
                $rlt = $rlt.$specialist[$cnt][0]->name.', ';
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
                $rlt[$cnt] = $tp->description.' - '.$tp->address;
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
                $rlt[$cnt] = new \stdClass();
                $rlt[$cnt]->title = $tp->title;
                $rlt[$cnt]->description = $tp->description;
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

        return view('home.filter')->with('doctors', $filter)->with('speciality', 'disable');
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
        $table = DB::table('appointments');

        $timeString = $request->time;
        $timeFormat = 'Y-m-d H:i:s';
        $time = \DateTime::createFromFormat($timeFormat, $timeString);
        $appointment_at = $time->format('Y-m-d H:i:s');

        $doctor_data = DB::table('doctors')->where('id', $request->doctor_id)->first();
        $clinic_id = $doctor_data->clinic_id;
        
        $doctor = [
            "id" => $doctor_data->id,
            "name" => explode('"', $doctor_data->name)[3],
            "price" => $doctor_data->price,
            "discount_price" => $doctor_data->discount_price,
            "enable_appointment" => $doctor_data->enable_appointment,
        ];
        $doctor = json_encode($doctor);

        $clinic_data = DB::table('clinics')->where('id', $clinic_id)->first();
        $clinic = [
            'id' => $clinic_data->id,
            'name' => explode('"', $clinic_data->name)[3],
            'phone_number' => $clinic_data->phone_number,
            'mobile_number' => $clinic_data->mobile_number,
        ];
        $clinic = json_encode($clinic);

        $patient_data = DB::table('patients')->where('id', $request->patient_id)->first();
        $user_id = $patient_data-> user_id;
        $patient = [
            'id' => $patient_data->id,
            'first_name' => $patient_data->first_name,
            'last_name' => $patient_data->last_name,
            'gender' => $patient_data->gender,
            'age' => $patient_data->age,
            'height' => $patient_data->height,
            'weight' => $patient_data->weight,
        ];
        $patient = json_encode($patient);

        $address_data = DB::table('addresses')->where('id', $request->address_id)->first();
        $address = [
            'id' => $address_data->id,
            'description' => $address_data->description,
            'address' => $address_data->address,
            'latitude' => $address_data->latitude,
            'longitude' => $address_data->longitude,
        ];
        $address = json_encode($address);

        $taxes_ids = DB::table('clinic_taxes')->where('clinic_id', $clinic_id)->get();
        $taxes = [];
        $cnt = 0;
        foreach($taxes_ids as $tax_id){
            $tp_tax_data = DB::table('taxes')->where('id', $tax_id->tax_id)->first();
            $tp_tax = [
                'id' => $tp_tax_data->id,
                'name' => $tp_tax_data->name,
                'value' => $tp_tax_data->value,
                'type' => $tp_tax_data->type,
            ];
            $taxes[$cnt ++] = $tp_tax;
        }
        $taxes = json_encode($taxes);

        $data = [
            'clinic' => $clinic,
            'doctor' => $doctor,
            'patient' => $patient,
            'user_id' => $user_id,
            'quantity' => 1,
            'appointment_status_id' => 1,
            'address' => $address,
            'payment_id' => null,
            'coupon' => null,
            'taxes' => $taxes,
            'appointment_at' => $appointment_at,
            'start_at' => null,
            'ends_at' => null,
            'hint' => $request->hint,
            'online' => 0,
            'cancel' => 0,
            "created_at"=> now(),
            "updated_at"=> now()
        ];

        if($table->insert($data)){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'failed']);
        }
    }

    public function speciality(Request $request){
        $doctor_ids = DB::table('doctor_specialities')->where('speciality_id', $request->id)->get();
        $cnt = 0;
        $doctors = [];
        foreach($doctor_ids as $doctor_id){
            $doctor = DB::table('doctors')->where('id', $doctor_id->doctor_id)->first();
            $doctors[$cnt ++] = $doctor;
        }
        $filter = $doctors;
        $cn = 0;
        foreach($filter as $tp){
            $specialisties = DB::table('doctor_specialities')
                ->where('doctor_id', $tp->id)->get();
            $cnt = 0;
            $rlt = "";
            foreach($specialisties as $tp){
                $specialist[$cnt] = DB::table('specialities')
                    ->where('id', $tp->speciality_id)->get();
                $rlt = $rlt.$specialist[$cnt][0]->name.', ';
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
                $rlt[$cnt] = $tp->description.' - '.$tp->address;
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
                $rlt[$cnt] = new \stdClass();
                $rlt[$cnt]->title = $tp->title;
                $rlt[$cnt]->description = $tp->description;
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
        $special = DB::table('specialities')->where('id', $request->id)->first()->name;
        return view('home.filter')->with('doctors', $filter)->with('speciality', $special);
    }

}
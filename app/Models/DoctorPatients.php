<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class DoctorPatients
 * @package App\Models
 * @version November 27, 2022, 9:26 pm CET
 *
 * @property integer doctor_id
 * @property integer patient_id
 */
class DoctorPatients extends Model
{

    public $table = 'doctor_patients';
    public $timestamps = false;


    public $fillable = [
        'doctor_id',
        'patient_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'doctor_id' => 'integer',
        'patient_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'doctor_id' => 'exists:doctors,id',
        'patient_id' => 'exists:patients,id'
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',

    ];

    public function customFieldsValues()
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }

    public function getCustomFieldsAttribute()
    {
        $hasCustomField = in_array(static::class,setting('custom_field_models',[]));
        if (!$hasCustomField){
            return [];
        }
        $array = $this->customFieldsValues()
            ->join('custom_fields','custom_fields.id','=','custom_field_values.custom_field_id')
            ->where('custom_fields.in_table','=',true)
            ->get()->toArray();

        return convertToAssoc($array,'name');
    }

    
    
}

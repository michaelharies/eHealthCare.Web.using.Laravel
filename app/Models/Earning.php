<?php
/*
 * File name: Earning.php
 * Last modified: 2021.01.30 at 15:09:09
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Earning
 * @package App\Models
 * @version January 30, 2021, 1:53 pm UTC
 *
 * @property Clinic clinic
 * @property integer clinic_id
 * @property integer total_appointments
 * @property double total_earning
 * @property double admin_earning
 * @property double clinic_earning
 * @property double taxes
 */
class Earning extends Model
{

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'clinic_id' => 'required|exists:clinics,id',
        'doctor_id' => 'required|exists:doctor,id'
    ];
    public $table = 'earnings';
    public $fillable = [
        'clinic_id',
        'doctor_id',
        'total_appointments',
        'total_earning',
        'admin_earning',
        'doctor_earning',
        'clinic_earning',
        'taxes'
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'clinic_id' => 'integer',
        'total_appointments' => 'integer',
        'total_earning' => 'double',
        'admin_earning' => 'double',
        'doctor_earning' => 'double',
        'clinic_earning' => 'double',
        'taxes' => 'double'
    ];
    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',

    ];

    public function getCustomFieldsAttribute()
    {
        $hasCustomField = in_array(static::class, setting('custom_field_models', []));
        if (!$hasCustomField) {
            return [];
        }
        $array = $this->customFieldsValues()
            ->join('custom_fields', 'custom_fields.id', '=', 'custom_field_values.custom_field_id')
            ->where('custom_fields.in_table', '=', true)
            ->get()->toArray();

        return convertToAssoc($array, 'name');
    }

    public function customFieldsValues()
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }

    /**
     * @return BelongsTo
     **/
    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'id');
    }

    /**
     * @return BelongsTo
     **/
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'id');
    }

}

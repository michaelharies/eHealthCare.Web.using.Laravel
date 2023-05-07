<?php
/*
 * File name: DoctorReview.php
 * Last modified: 2021.01.31 at 21:06:59
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class DoctorReview
 * @package App\Models
 * @version January 23, 2021, 7:42 pm UTC
 *
 * @property User user
 * @property Doctor doctor
 * @property string review
 * @property double rate
 * @property integer user_id
 * @property integer doctor_id
 */
class DoctorReview extends Model
{

    public $table = 'doctor_reviews';


    public $fillable = [
        'review',
        'rate',
        'user_id',
        'doctor_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'review' => 'string',
        'rate' => 'double',
        'doctor_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'rate' => 'required|numeric|max:5|min:0',
        'user_id' => 'required|exists:users,id',
        'doctor_id' => 'required|exists:doctors,id'
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

    /**
     * @return BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     **/
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'id');
    }

}

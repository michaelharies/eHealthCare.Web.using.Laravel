<?php

namespace App\Models;

use Brick\Math\BigInteger;
use Decimal\Decimal;
use Eloquent as Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ClinicReview
 * @package App\Models
 * @version October 23, 2022, 5:11 pm AST
 *
 * @property User user
 * @property Clinic clinic
 * @property string review
 * @property decimal rate
 * @property bigInteger user_id
 * @property integer clinic_id
 */
class ClinicReview extends Model
{

    public $table = 'clinic_reviews';
    


    public $fillable = [
        'review',
        'rate',
        'user_id',
        'clinic_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'review' => 'string',
        'rate' => 'double',
        'clinic_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'rate' => 'required|numeric|max:5|min:0',
        'user_id' => 'required|exists:users,id',
        'clinic_id' => 'required|exists:clinics,id'
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     **/
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'id');
    }
    
}

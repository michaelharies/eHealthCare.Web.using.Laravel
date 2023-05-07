<?php
/*
 * File name: Appointment.php
 * Last modified: 2021.09.15 at 13:38:29
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Models;

use App\Casts\OptionCollectionCast;
use App\Casts\TaxCollectionCast;
use App\Events\AppointmentCreatingEvent;
use Carbon\Carbon;
use Eloquent as Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Date;

/**
 * Class Appointment
 * @package App\Models
 * @version January 25, 2021, 9:22 pm UTC
 *
 * @property int id
 * @property User user
 * @property AppointmentStatus appointmentStatus
 * @property Payment payment
 * @property Clinic clinic
 * @property Patient patient
 * @property Doctor doctor
 * @property Option[] options
 * @property integer quantity
 * @property integer user_id
 * @property integer address_id
 * @property integer appointment_status_id
 * @property integer payment_status_id
 * @property Address address
 * @property integer payment_id
 * @property double duration
 * @property Coupon coupon
 * @property Tax[] taxes
 * @property Date appointment_at
 * @property Date start_at
 * @property Date ends_at
 * @property string hint
 * @property boolean online
 * @property boolean cancel
 */
class Appointment extends Model
{

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required|exists:users,id',
        'appointment_status_id' => 'required|exists:appointment_statuses,id',
        'payment_id' => 'nullable|exists:payments,id'
    ];
    public $table = 'appointments';
    public $fillable = [
        'clinic',
        'doctor',
        'patient',
        'user_id',
        'appointment_status_id',
        'address',
        'payment_id',
        'coupon',
        'taxes',
        'appointment_at',
        'start_at',
        'ends_at',
        'hint',
        'online',
        'cancel'
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'clinic' => Clinic::class,
        'doctor' => Doctor::class,
        'patient' => Patient::class,
        'address' => Address::class,
        'coupon' => Coupon::class,
        'taxes' => TaxCollectionCast::class,
        'appointment_status_id' => 'integer',
        'payment_id' => 'integer',
        'user_id' => 'integer',
        'appointment_at' => 'datetime:Y-m-d\TH:i:s.uP',
        'start_at' => 'datetime:Y-m-d\TH:i:s.uP',
        'ends_at' => 'datetime:Y-m-d\TH:i:s.uP',
        'hint' => 'string',
        'online' => 'boolean',
        'cancel' => 'boolean'
    ];
    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'creating' => AppointmentCreatingEvent::class,
        'updating' => AppointmentCreatingEvent::class,
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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     **/
    public function appointmentStatus(): BelongsTo
    {
        return $this->belongsTo(AppointmentStatus::class, 'appointment_status_id', 'id');
    }

    /**
     * @return BelongsTo
     **/
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }

    public function getTotal(): float
    {
        $total = $this->getSubtotal();
        $total += $this->getTaxesValue();
        $total += $this->getCouponValue();
        return $total;
    }

    public function getSubtotal(): float
    {
        return $this->doctor->getPrice() ;
    }

    public function getTaxesValue(): float
    {
        $total = $this->getSubtotal();
        $taxValue = 0;
        foreach ($this->taxes as $tax) {
            if ($tax->type == 'percent') {
                $taxValue += ($total * $tax->value / 100);
            } else {
                $taxValue += $tax->value;
            }
        }
        return $taxValue;
    }

    public function getCouponValue(): float
    {
        $total = $this->getSubtotal();
        if (empty($this->coupon)) {
            return 0;
        } else {
            if ($this->coupon->discount_type == 'percent') {
                return -($total * $this->coupon->discount / 100);
            } else {
                return -$this->coupon->discount;
            }
        }
    }

}

<?php
/*
 * File name: Doctor.php
 * Last modified: 2022.01.05 at 22:45:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Models;

use App\Casts\DoctorCast;
use App\Traits\HasTranslations;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;
use Spatie\OpeningHours\OpeningHours;

/**
 * Class Doctor
 * @package App\Models
 * @version January 19, 2021, 1:59 pm UTC
 *
 * @property Collection speciality
 * @property Clinic clinic
 * @property User user
 * @property Collection Option
 * @property Collection DoctorsReview
 * @property Collection[] availabilityHours
 * @property integer id
 * @property double price
 * @property double discount_price
 * @property string description
 * @property string name
 * @property boolean featured
 * @property boolean enable_appointment
 * @property boolean enable_at_clinic
 * @property boolean enable_at_customer_address
 * @property boolean enable_online_consultation
 * @property boolean available
 * @property integer clinic_id
 * @property integer user_id
 */
class Doctor extends Model implements HasMedia, Castable
{
    use HasMediaTrait {
        getFirstMediaUrl as protected getFirstMediaUrlTrait;
    }

    use HasTranslations;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:127',
        'price' => 'required|numeric|min:0|max:99999999,99',
        'discount_price' => 'nullable|numeric|min:0|max:99999999,99',
        'description' => 'required',
        'clinic_id' => 'required|exists:clinics,id',
        //'user_id' => 'required|exists:users,id'
    ];
    public $translatable = [
        'name',
        'description',
    ];
    public $table = 'doctors';
    public $fillable = [
        'name',
        'price',
        'discount_price',
        'description',
        'featured',
        'enable_appointment',
        'enable_at_clinic',
        'enable_at_customer_address',
        'enable_online_consultation',
        'available',
        'clinic_id',
        'user_id'
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'image' => 'string',
        'name' => 'string',
        'price' => 'double',
        'discount_price' => 'double',
        'description' => 'string',
        'featured' => 'boolean',
        'enable_appointment' => 'boolean',
        'enable_at_clinic' => 'boolean',
        'enable_at_customer_address' => 'boolean',
        'enable_online_consultation' => 'boolean',
        'available' => 'boolean',
        'clinic_id' => 'integer',
        'user_id' => 'integer',
        'rate' => 'double',
        'total_reviews' => 'integer'
    ];
    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',
        'has_media',
        'available',
        'total_reviews',
        'is_favorite',
        'rate'
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    /**
     * @return CastsAttributes|CastsInboundAttributes|string
     */
    public static function castUsing()
    {
        return DoctorCast::class;
    }

    /**
     * @param Media|null $media
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_CROP, 200, 200)
            ->sharpen(10);

        $this->addMediaConversion('icon')
            ->fit(Manipulations::FIT_CROP, 100, 100)
            ->sharpen(10);
    }

    /**
     * to generate media url in case of fallback will
     * return the file type icon
     * @param string $conversion
     * @return string url
     */
    public function getFirstMediaUrl($collectionName = 'default', string  $conversion = ''): string
    {
        $url = $this->getFirstMediaUrlTrait($collectionName);
        $array = explode('.', $url);
        $extension = strtolower(end($array));
        if (in_array($extension, config('medialibrary.extensions_has_thumb'))) {
            return asset($this->getFirstMediaUrlTrait($collectionName, $conversion));
        } else {
            return asset(config('medialibrary.icons_folder') . '/' . $extension . '.png');
        }
    }

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
     * Add Media to api results
     * @return bool
     */
    public function getHasMediaAttribute(): bool
    {
        return $this->hasMedia('image');
    }

    public function openingHours(): OpeningHours
    {
        $openingHoursArray = [];
        foreach ($this->availabilityHours as $element) {
            $openingHoursArray[$element['day']] = [
                'data' => $element['data'],
                $element['start_at'] . '-' . $element['end_at']
            ];
        }
        return OpeningHours::createAndMergeOverlappingRanges($openingHoursArray);
    }


    public function scopeNear($query, $latitude, $longitude, $areaLatitude, $areaLongitude)
    {
        // Calculate the distant in mile
        $distance = "SQRT(
                    POW(69.1 * (addresses.latitude - $latitude), 2) +
                    POW(69.1 * ($longitude - addresses.longitude) * COS(addresses.latitude / 57.3), 2))";

        // Calculate the distant in mile
        $area = "SQRT(
                    POW(69.1 * (addresses.latitude - $areaLatitude), 2) +
                    POW(69.1 * ($areaLongitude - addresses.longitude) * COS(addresses.latitude / 57.3), 2))";

        // convert the distance to KM if the distance unit is KM
        if (setting('distance_unit') == 'km') {
            $distance .= " * 1.60934"; // 1 Mile = 1.60934 KM
            $area .= " * 1.60934"; // 1 Mile = 1.60934 KM
        }

        return $query
            ->join('clinics','doctors.clinic_id','=','clinics.id')
            ->join('addresses', 'clinics.address_id', '=', 'addresses.id')
            ->whereRaw("$distance < clinics.availability_range")
            ->select(DB::raw($distance . " AS distance"), DB::raw($area . " AS area"), "doctors.*")
            ->orderBy('area');
    }

    /**
     * get each range of 30 min with open/close clinic
     */
    public function weekCalendarRange(Carbon $date): array
    {
        $period = CarbonPeriod::since($date->subDay()->ceilDay()->subMinutes(30))->minutes(30)->until($date->addDay()->ceilDay()->subMinutes(60));
        $dates = [];
        // Iterate over the period
        foreach ($period as $key => $d) {
            $firstDate = $d->locale('en')->toDateTime();
            $secondDate = $d->locale('en')->addMinutes(30)->toDateTime();
            $isOpen = $this->openingHours()->isOpenAt($firstDate) || $this->openingHours()->isOpenAt($secondDate);
            $times = [$d->locale('en')->toIso8601String()];
            $times[] = $d->locale('en')->addMinutes(30)->toIso8601String();
            $dates[] = [$times, $isOpen];
        }
        return $dates;
    }

    /**
     * get each 15 min with open/close clinic
     */
    public function weekCalendar(Carbon $date): array
    {
        $period = CarbonPeriod::since($date->subDay()->ceilDay())->minutes(15)->until($date->addDay()->ceilDay()->subMinutes(15));
        $dates = [];
        // Iterate over the period
        foreach ($period as $key => $d) {
            $firstDate = $d->locale('en')->toDateTime();
            $isOpen = $this->openingHours()->isOpenAt($firstDate);
            $times = $d->locale('en')->toIso8601String();
            $dates[] = [$times, $isOpen];
        }
        return $dates;
    }

    /**
     * Check if is a favorite for current user
     * @return bool
     */
    public function getIsFavoriteAttribute(): bool
    {
        return $this->favorites()->count() > 0;
    }

    /**
     * @return HasMany
     **/
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'doctor_id')->where('favorites.user_id', auth()->id());
    }

    /**
     * Add Total Reviews to api results
     * @return int
     */
    public function getTotalReviewsAttribute(): int
    {
        return $this->doctorReviews()->count();
    }

    /**
     * @return HasMany
     **/
    public function doctorReviews(): HasMany
    {
        return $this->hasMany(DoctorReview::class, 'doctor_id');
    }

    /**
     * Add Rate to api results
     * @return float
     */
    public function getRateAttribute(): float
    {
        return (float)$this->doctorReviews()->avg('rate');
    }

    /**
     * Doctor available when
     * This Doctor is marked as available
     * and his
     * Provider is ready so he is accepted by admin and marked as available and is open now
     */
    public function getAvailableAttribute(): bool
    {
        return isset($this->attributes['available']) && $this->attributes['available'] && isset($this->clinic) && $this->openingHours()->isOpen();
    }

    /**
     * @return BelongsTo
     **/
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'id');
    }

    /**
     * @return BelongsTo
     **/
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    /**
     * @return BelongsToMany
     **/
    public function specialities(): BelongsToMany
    {
        return $this->belongsToMany(Speciality::class, 'doctor_specialities');
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->discount_price > 0 ? $this->discount_price : $this->price;
    }

    /**
     * @return bool
     */
    public function hasDiscount(): bool
    {
        return $this->discount_price > 0;
    }

    public function discountables()
    {
        return $this->morphMany('App\Models\Discountable', 'discountable');
    }

    /**
     * @return HasMany
     **/
    public function availabilityHours(): HasMany
    {
        return $this->hasMany(AvailabilityHour::class, 'doctor_id')->orderBy('start_at');
    }

    /**
     * @return BelongsToMany
     **/
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'doctor_patients');
    }
}

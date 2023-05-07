<?php
/*
 * File name: Clinic.php
 * Last modified: 2022.04.11 at 13:30:03
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Models;

use App\Casts\ClinicCast;
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
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Nwidart\Modules\Exceptions\ModuleNotFoundException;
use Nwidart\Modules\Facades\Module;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;
use Spatie\OpeningHours\OpeningHours;

/**
 * Class Clinic
 * @package App\Models
 * @version January 13, 2021, 11:11 am UTC
 *
 * @property Collection[] taxes
 * @property ClinicLevel clinicLevel
 * @property Collection[] users
 * @property Collection[] awards
 * @property Collection[] experiences
 * @property Collection ClinicsReview
 * @property Collection clinicSubscriptions
 * @property Collection[] doctors
 * @property Collection[] galleries
 * @property integer clinic_level_id
 * @property integer id
 * @property string name
 * @property Address address
 * @property string description
 * @property string phone_number
 * @property string mobile_number
 * @property double availability_range
 * @property boolean available
 * @property boolean featured
 * @property boolean accepted
 */
class Clinic extends Model implements HasMedia, Castable
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
        'address_id' => 'required|exists:addresses,id',
        'clinic_level_id' => 'required|exists:clinic_levels,id',
        'phone_number' => 'max:50',
        'mobile_number' => 'max:50',
        'availability_range' => 'required|numeric|max:9999999.99|min:0.01'
    ];
    public $translatable = [
        'name',
        'description',
    ];
    public $table = 'clinics';

    public $fillable = [
        'name',
        'description',
        'clinic_level_id',
        'address_id',
        'phone_number',
        'mobile_number',
        'availability_range',
        'available',
        'featured',
        'accepted'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'image' => 'string',
        'name' => 'string',
        'description' => 'string',
        'address_id' => 'integer',
        'clinic_level_id' => 'integer',
        'phone_number' => 'string',
        'mobile_number' => 'string',
        'availability_range' => 'double',
        'available' => 'boolean',
        'featured' => 'boolean',
        'accepted' => 'boolean',
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
        'has_valid_subscription',
        'total_reviews',
        'total_appointments',
        'rate'
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    /**
     * @return string
     */
    public static function castUsing(): string
    {
        return ClinicCast::class;
    }

    public function discountables(): MorphMany
    {
        return $this->morphMany('App\Models\Discountable', 'discountable');
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
    public function getFirstMediaUrl($collectionName = 'default', $conversion = ''): string
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

    public function getCustomFieldsAttribute(): array
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

    public function customFieldsValues(): MorphMany
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }

    /**
     * Provider ready when he is accepted by admin and marked as available
     * and is open now
     */
    public function getAvailableAttribute(): bool
    {
        return $this->accepted && $this->attributes['available'];
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
            ->join('addresses', 'clinics.address_id', '=', 'addresses.id')
            ->whereRaw("$distance < clinics.availability_range")
            ->select(DB::raw($distance . " AS distance"), DB::raw($area . " AS area"), "clinics.*")
            ->orderBy('area');
    }



    public function getHasValidSubscriptionAttribute(): ?bool
    {
        if (!Module::isActivated('Subscription')) {
            return null;
        }
        $result = $this->clinicSubscriptions
            ->where('expires_at', '>', now())
            ->where('starts_at', '<=', now())
            ->where('active', '=', 1)
            ->count();
        return $result > 0;
    }

    /**
     * @return BelongsTo
     **/
    public function clinicLevel(): BelongsTo
    {
        return $this->belongsTo(ClinicLevel::class, 'clinic_level_id', 'id');
    }
    /**
     * @return HasMany
     **/
    public function awards(): HasMany
    {
        return $this->hasMany(Award::class, 'clinic_id');
    }

    /**
     * @return HasMany
     **/
    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class, 'clinic_id');
    }

    /**
     * @return null|HasMany
     *
     * @throws ModuleNotFoundException
     */
    public function clinicSubscriptions(): HasMany
    {
        if (Module::isActivated('Subscription'))
            return $this->hasMany('Modules\Subscription\Models\ClinicSubscription', 'clinic_id');
        else
            throw new ModuleNotFoundException();

    }

    /**
     * @return HasMany
     **/
    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class, 'clinic_id');
    }
    /**
 * @return HasMany
 **/
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'clinic->id')->orderBy('appointment_at');
    }

    public function getTotalAppointmentsAttribute(): float
    {
        return $this->appointments()->count();
    }

    /**
     * @return BelongsToMany
     **/
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'clinic_users');
    }

    /**
     * @return HasMany
     **/
    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class, 'clinic_id');
    }

    /**
     * @return BelongsTo
     **/
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    /**
     * @return BelongsToMany
     **/
    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class, 'clinic_taxes');
    }

    /**
     * Add Media to api results
     * @return bool
     */
    public function getHasMediaAttribute(): bool
    {
        return $this->hasMedia('image');
    }

    /**
     * Add Total Reviews to api results
     * @return int
     */
    public function getTotalReviewsAttribute(): int
    {
        return $this->clinicReviews()->count();
    }

    /**
     * @return HasMany
     **/
    public function clinicReviews(): HasMany
    {
        return $this->hasMany(ClinicReview::class, 'clinic_id');
    }

    /**
     * Add Rate to api results
     * @return float
     */
    public function getRateAttribute(): float
    {
        return (float)$this->clinicReviews()->avg('rate');
    }
}

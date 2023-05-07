<?php

namespace App\Models;

use App\Casts\PatientCast;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

/**
 * Class Patient
 * @package App\Models
 * @version October 17, 2022, 4:38 pm CEST
 *
 * @property User user
 * @property integer id
 * @property integer user_id
 * @property integer clinic_id
 * @property string first_name
 * @property string last_name
 * @property string phone_number
 * @property string mobile_number
 * @property string age
 * @property string gender
 * @property string weight
 * @property string height
 */
class Patient extends Model implements HasMedia, Castable
{
    use HasMediaTrait {
        getFirstMediaUrl as protected getFirstMediaUrlTrait;
    }

    public $table = 'patients';



    public $fillable = [
        'user_id',
        'clinic_id',
        'first_name',
        'last_name',
        'phone_number',
        'mobile_number',
        'age',
        'gender',
        'weight',
        'height'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'image' => 'string',
        'card_id' => 'string',
        'user_id' => 'integer',
        'clinic_id' => 'integer',
        'first_name' => 'string',
        'last_name' => 'string',
        'phone_number' => 'string',
        'mobile_number' => 'string',
        'age' => 'string',
        'gender' => 'string',
        'weight' => 'string',
        'height' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required|exists:users,id',
        'first_name' => 'required|max:127',
        'last_name' => 'required|max:127',
        'phone_number' => 'max:50',
        'mobile_number' => 'max:50',
        'age' => 'required|max:127',
        'gender' => 'required|max:127',
        'weight' => 'required|max:127',
        'height' => 'required|max:127'
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',
        'total_appointments',

    ];
    /**
     * @return CastsAttributes|CastsInboundAttributes|string
     */
    public static function castUsing(): string
    {
        return PatientCast::class;
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
    public function getFirstMediaUrl($collectionName = 'default', string $conversion = ''): string
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

    public function getCustomFieldsAttribute(): array
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
     * @return HasMany
     **/
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient->id')->orderBy('appointment_at');
    }

    public function getTotalAppointmentsAttribute(): float
    {
        return $this->appointments()->count();
    }

    /**
     * @return BelongsToMany
     **/
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_patients');
    }


}

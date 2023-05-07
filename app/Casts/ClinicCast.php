<?php
/*
 * File name: ClinicCast.php
 * Last modified: 2022.02.02 at 19:14:22
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Casts;

use App\Models\Clinic;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

/**
 * Class ClinicCast
 * @package App\Casts
 */
class ClinicCast implements CastsAttributes
{

    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes): Clinic
    {
        $decodedValue = json_decode($value, true);
        $clinic = Clinic::find($decodedValue['id']);
        // clinic exist in database
        if (!empty($clinic)) {
            return $clinic;
        }
        // if not exist the clone will loaded
        // create new clinic based on values stored on database
        $clinic = new Clinic($decodedValue);
        // push id attribute fillable array
        array_push($clinic->fillable, 'id');
        // assign the id to clinic object
        $clinic->id = $decodedValue['id'];
        return $clinic;
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes): array
    {
//        if (!$value instanceof Clinic) {
//            throw new InvalidArgumentException('The given value is not an Clinic instance.');
//        }
        return [
            'clinic' => json_encode([
                'id' => $value['id'],
                'name' => $value['name'],
                'phone_number' => $value['phone_number'],
                'mobile_number' => $value['mobile_number'],
            ])
        ];
    }
}

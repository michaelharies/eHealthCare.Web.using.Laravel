<?php
/*
 * File name: DoctorCast.php
 * Last modified: 2021.11.21 at 21:35:24
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Casts;

use App\Models\Doctor;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use PhpParser\Comment\Doc;

/**
 * Class DoctorCast
 * @package App\Casts
 */
class DoctorCast implements CastsAttributes
{

    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes): Doctor
    {
        $decodedValue = json_decode($value, true);
        $doctor = Doctor::find($decodedValue['id']);
        // clinic exist in database
        if (!empty($doctor)) {
            return $doctor;
        }
        // if not exist the clone will loaded
        // create new clinic based on values stored on database
        $doctor = new Doctor($decodedValue);
        // push id attribute fillable array
        array_push($doctor->fillable, 'id');
        // assign the id to clinic object
        $doctor->id = $decodedValue['id'];
        return $doctor;
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes): array
    {
//        if (!$value instanceof Doctor) {
//            throw new InvalidArgumentException('The given value is not a Doctor instance.');
//        }
        return [
            'doctor' => json_encode(
                [
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'price' => $value['price'],
                    'discount_price' => $value['discount_price'],
                    'enable_appointment' => $value['enable_appointment'],
                ]
            )
        ];
    }
}

<?php
/*
 * File name: PatientCast.php
 * Last modified: 2021.11.21 at 21:35:24
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Casts;

use App\Models\Patient;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

/**
 * Class PatientCast
 * @package App\Casts
 */
class PatientCast implements CastsAttributes
{

    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes): Patient
    {
        $decodedValue = json_decode($value, true);
        $patient = Patient::find($decodedValue['id']);
        // service exist in database
        if (!empty($patient)) {
            return $patient;
        }
        // if not exist the clone will load
        // create new patient based on values stored on database
        $patient = new Patient($decodedValue);
        // push id attribute fillable array
        array_push($patient->fillable, 'id');
        // assign the id to patient object
        $patient->id = $decodedValue['id'];
        return $patient;
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes): array
    {
//        if (!$value instanceof Patient) {
//            throw new InvalidArgumentException('The given value is not a Patient instance.');
//        }

        return [
            'patient' => json_encode(
                [
                    'id' => $value['id'],
                    'first_name' => $value['first_name'],
                    'last_name' => $value['last_name'],
                    'gender' => $value['gender'],
                    'age' => $value['age'],
                    'height' => $value['height'],
                    'weight' => $value['weight'],
                ]
            )
        ];
    }
}

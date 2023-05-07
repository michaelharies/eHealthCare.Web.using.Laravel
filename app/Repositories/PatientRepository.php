<?php

namespace App\Repositories;

use App\Models\Patient;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PatientRepository
 * @package App\Repositories
 * @version October 17, 2022, 4:38 pm CEST
 *
 * @method Patient findWithoutFail($id, $columns = ['*'])
 * @method Patient find($id, $columns = ['*'])
 * @method Patient first($columns = ['*'])
*/
class PatientRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
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
     * Configure the Model
     **/
    public function model(): string
    {
        return Patient::class;
    }
}

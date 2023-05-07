<?php

namespace App\Repositories;

use App\Models\DoctorPatients;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DoctorPatientsRepository
 * @package App\Repositories
 * @version November 27, 2022, 9:26 pm CET
 *
 * @method DoctorPatients findWithoutFail($id, $columns = ['*'])
 * @method DoctorPatients find($id, $columns = ['*'])
 * @method DoctorPatients first($columns = ['*'])
*/
class DoctorPatientsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'doctor_id',
        'patient_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DoctorPatients::class;
    }
}

<?php
/*
 * File name: DoctorOfEClinicCriteria.php
 * Last modified: 2022.04.13 at 08:09:50
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Criteria\Patients;


use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class PatientOfClinicCriteria.
 *
 * @package namespace App\Criteria\Doctors;
 */
class PatientOfDoctorCriteria implements CriteriaInterface
{
    /**
     * @var int
     */
    private $userId;

    /**
     * DoctorOfEClinicCriteria constructor.
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
    if (auth()->check() && auth()->user()->hasRole('doctor')) {
            return $model
                ->join('doctors','doctor_patients.doctor_id','=','doctors.id')
                ->where('doctors.user_id','=', '5')
                ->select('doctor_patients.*');
        } else {
            return $model->select('patients.*')->groupBy('patients.id');
        }
    }
}

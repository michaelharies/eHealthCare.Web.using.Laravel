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
class PatientOfClinicCriteria implements CriteriaInterface
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
    if (auth()->check() && auth()->user()->hasRole('clinic_owner')) {
            return $model->join('clinics', 'clinics.id', '=', 'patients.clinic_id')
                ->groupBy('patients.id')
                ->where('clinics.id', $this->userId)
                ->select('patients.*');
        } else {
            return $model->select('patients.*')->groupBy('patients.id');
        }
    }
}

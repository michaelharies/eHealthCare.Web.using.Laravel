<?php
/*
 * File name: AppointmentOfClinicCriteria.php
 * Last modified: 2021.02.22 at 14:52:03
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\Appointments;

use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AppointmentOfClinicCriteria.
 *
 * @package namespace App\Criteria\Appointments;
 */
class AppointmentOfDoctorCriteria implements CriteriaInterface
{
    /**
     * @var int
     */
    private $doctor;

    /**
     * AppointmentOfClinicCriteria constructor.
     */
    public function __construct($doctor)
    {
        $this->doctor = $doctor;
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
        $doctor = DB::raw("json_extract(doctor, '$.id')");
        return $model->where($doctor, $this->doctor)
            ->where('payment_status_id', '2')
            ->groupBy('appointments.id')
            ->select('appointments.*');

    }
}

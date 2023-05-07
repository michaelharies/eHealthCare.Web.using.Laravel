<?php
/*
 * File name: AppointmentsOfPatientCriteria.php
 * Last modified: 2021.05.07 at 19:12:31
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\Appointments;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AppointmentsOfPatientCriteria.
 *
 * @package namespace App\Criteria\Appointments;
 */
class AppointmentsOfPatientCriteria implements CriteriaInterface
{
    /**
     * @var User
     */
    private $userId;

    /**
     * AppointmentsOfPatientCriteria constructor.
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
        if (auth()->user()->hasRole('admin')) {
            return $model;
        } else if (auth()->user()->hasRole('clinic_owner')) {
            $clinicId = DB::raw("json_extract(clinic, '$.id')");
            return $model->join("clinic_users", "clinic_users.clinic_id", "=", $clinicId)
                ->where('clinic_users.user_id', $this->userId)
                ->groupBy('appointments.id')
                ->select('appointments.*');

        } else if (auth()->user()->hasRole('doctor')) {
            $doctorId = DB::raw("json_extract(doctor, '$.id')");
            return $model->join("doctors", "doctors.id", "=", $doctorId)
                ->where('doctors.user_id', $this->userId)
                ->groupBy('appointments.id')
                ->select('appointments.*');

        }else if (auth()->user()->hasRole('customer')) {
            return $model->where('appointments.user_id', $this->userId)
                ->select('appointments.*')
                ->groupBy('appointments.id');
        } else {
            return $model;
        }
    }
}

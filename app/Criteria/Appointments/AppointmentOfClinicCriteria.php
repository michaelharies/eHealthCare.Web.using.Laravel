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
class AppointmentOfClinicCriteria implements CriteriaInterface
{
    /**
     * @var int
     */
    private $clinic;

    /**
     * AppointmentOfClinicCriteria constructor.
     */
    public function __construct($clinic)
    {
        $this->clinic = $clinic;
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
        $clinic = DB::raw("json_extract(clinic, '$.id')");
        return $model->where($clinic, $this->clinic)
            ->where('payment_status_id', '2')
            ->groupBy('appointments.id')
            ->select('appointments.*');

    }
}

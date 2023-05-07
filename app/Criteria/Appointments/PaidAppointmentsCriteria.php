<?php
/*
 * File name: PaidAppointmentsCriteria.php
 * Last modified: 2021.02.22 at 14:23:36
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\Appointments;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class PaidAppointmentsCriteria.
 *
 * @package namespace App\Criteria\Appointments;
 */
class PaidAppointmentsCriteria implements CriteriaInterface
{
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
        return $model->join('payments', 'payments.id', '=', 'appointments.payment_id')
            ->where('payments.payment_status_id', '2') // Paid Id
            ->groupBy('appointments.id')
            ->select('appointments.*');

    }
}

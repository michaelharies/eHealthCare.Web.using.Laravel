<?php
/*
 * File name: AvailabilityHoursOfUserCriteria.php
 * Last modified: 2021.03.23 at 11:46:05
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\AvailabilityHours;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AvailabilityHoursOfUserCriteria.
 *
 * @package namespace App\Criteria\AvailabilityHours;
 */
class AvailabilityHoursOfUserCriteria implements CriteriaInterface
{
    /**
     * @var int
     */
    private $userId;

    /**
     * AvailabilityHoursOfUserCriteria constructor.
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
            return $model->with("doctor")
                ->join("doctors", "doctors.id", "=", "availability_hours.doctor_id")
                ->join("clinic_users", "clinic_users.clinic_id", "=", "doctors.clinic_id")
                ->where('clinic_users.user_id', $this->userId)
                ->groupBy('availability_hours.id')
                ->select('availability_hours.*');
        }
        if (auth()->check() && auth()->user()->hasRole('doctor')) {
            return $model->join('doctors', 'doctors.id', '=', 'availability_hours.doctor_id')
                ->groupBy('availability_hours.id')
                ->select('availability_hours.*')
                ->where('doctors.user_id', $this->userId);
        }else {
            return $model;
        }
    }
}

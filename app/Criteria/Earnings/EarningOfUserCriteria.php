<?php
/*
 * File name: EarningOfUserCriteria.php
 * Last modified: 2021.02.21 at 14:50:32
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\Earnings;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class EarningOfUserCriteria.
 *
 * @package namespace App\Criteria\Earnings;
 */
class EarningOfUserCriteria implements CriteriaInterface
{
    private $userId;

    /**
     * EarningOfUserCriteria constructor.
     * @param $clinic
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }


    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        if (auth()->user()->hasRole('admin')) {
            return $model;
        }else if((auth()->user()->hasRole('clinic_owner'))){
            return $model->join("clinic_users", "clinic_users.clinic_id", "=", "earnings.clinic_id")
                ->groupBy('earnings.id')
                ->where('clinic_users.user_id', $this->userId);
        }else if((auth()->user()->hasRole('doctor'))){
            return $model->join("doctors", "doctors.id", "=", "earnings.doctor_id")
                ->groupBy('earnings.id')
                ->where('doctors.user_id', $this->userId);
        }else{
            return $model;
        }
    }
}

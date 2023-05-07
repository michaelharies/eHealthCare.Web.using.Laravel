<?php
/*
 * File name: DoctorsOfUserCriteria.php
 * Last modified: 2021.03.23 at 11:38:55
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\Doctors;

use Illuminate\Support\Facades\Log;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class DoctorsOfUserCriteria.
 *
 * @package namespace App\Criteria\Doctors;
 */
class DoctorsOfUserCriteria implements CriteriaInterface
{
    /**
     * @var int
     */
    private $userId;

    /**
     * DoctorsOfUserCriteria constructor.
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
        if (auth()->check() && auth()->user()->hasRole('admin')) {
            return $model;
        }else if (auth()->check() && auth()->user()->hasRole('clinic_owner')) {
            return $model->join('clinic_users', 'clinic_users.clinic_id', '=', 'doctors.clinic_id')
                ->groupBy('doctors.id')
                ->where('clinic_users.user_id', $this->userId)
                ->select('doctors.*');
        } else if (auth()->check() && auth()->user()->hasRole('doctor')) {
            return $model->join('users', 'users.id', '=', 'doctors.user_id')
                ->groupBy('doctors.id')
                ->where('doctors.user_id', $this->userId)
                ->select('doctors.*');
        }
        else {
            return $model;
        }
    }
}

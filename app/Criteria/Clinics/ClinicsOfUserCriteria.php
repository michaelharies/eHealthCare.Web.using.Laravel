<?php
/*
 * File name: ClinicsOfUserCriteria.php
 * Last modified: 2022.02.02 at 21:26:20
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Criteria\Clinics;

use App\Models\User;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ClinicsOfUserCriteria.
 *
 * @package namespace App\Criteria\Clinics;
 */
class ClinicsOfUserCriteria implements CriteriaInterface
{

    /**
     * @var User
     */
    private $userId;

    /**
     * ClinicsOfUserCriteria constructor.
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
            return $model->join('clinic_users', 'clinic_users.clinic_id', '=', 'clinics.id')
                ->select('clinics.*')
                ->where('clinic_users.user_id', $this->userId);
        } else if (auth()->user()->hasRole('doctor')) {
            return $model->join('doctors', 'doctors.clinic_id', '=', 'clinics.id')
                ->select('clinics.*')
                ->where('doctors.user_id', $this->userId);
        }
        else {
            return $model;
        }
    }
}

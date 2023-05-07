<?php
/*
 * File name: DoctorOfEClinicCriteria.php
 * Last modified: 2022.04.13 at 08:09:50
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Criteria\Doctors;


use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class DoctorOfEClinicCriteria.
 *
 * @package namespace App\Criteria\Doctors;
 */
class DoctorOfClinicCriteria implements CriteriaInterface
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
            return $model->join('clinic_users', 'clinic_users.clinic_id', '=', 'doctors.clinic_id')
                ->groupBy('doctors.id')
                ->where('clinic_users.user_id', $this->userId)
                ->select('doctors.*');
        } else {
            return $model->select('doctors.*')->groupBy('doctors.id');
        }
    }
}

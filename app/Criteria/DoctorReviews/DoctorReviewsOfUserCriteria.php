<?php
/*
 * File name: DoctorReviewsOfUserCriteria.php
 * Last modified: 2021.03.23 at 11:47:29
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\DoctorReviews;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class DoctorReviewsOfUserCriteria.
 *
 * @package namespace App\Criteria\DoctorReviews;
 */
class DoctorReviewsOfUserCriteria implements CriteriaInterface
{
    /**
     * @var int
     */
    private $userId;

    /**
     * DoctorReviewsOfUserCriteria constructor.
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
            return $model->select('doctor_reviews.*');
        } else if (auth()->check() && auth()->user()->hasRole('clinic_owner')) {
            return $model->join("clinic_users", "clinic_users.clinic_id", "=", "doctors.clinic_id")
                ->where('clinic_users.user_id', $this->userId)
                ->groupBy('doctor_reviews.id')
                ->select('doctor_reviews.*');
        } else if (auth()->check() && auth()->user()->hasRole('doctor')) {
            return $model->join("doctors", "doctors.id", "=", "doctor_reviews.doctor_id")
                ->where('doctors.user_id', $this->userId)
                ->groupBy('doctor_reviews.id')
                ->select('doctor_reviews.*');
        }
        else if (auth()->check() && auth()->user()->hasRole('customer')) {
            return $model->newQuery()->where('doctor_reviews.user_id', $this->userId)
                ->select('doctor_reviews.*');
        } else {
            return $model->select('doctor_reviews.*');
        }
    }
}

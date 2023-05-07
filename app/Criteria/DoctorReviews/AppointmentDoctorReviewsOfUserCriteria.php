<?php
/*
 * File name: AppointmentDoctorReviewsOfUserCriteria.php
 * Last modified: 2021.02.21 at 14:50:32
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\DoctorReviews;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AppointmentDoctorReviewsOfUserCriteria.
 *
 * @package namespace App\Criteria\DoctorReviews;
 */
class AppointmentDoctorReviewsOfUserCriteria implements CriteriaInterface
{
    /**
     * @var int
     */
    private $userId;

    /**
     * AppointmentDoctorReviewsOfUserCriteria constructor.
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
            return $model->select('doctor_reviews.*');
        } else if (auth()->user()->hasRole('clinic_owner')) {
            return $model->join("doctors", "doctors.id", "=", "doctor_reviews.doctor_id")
                ->join("clinic_users", "clinic_users.clinic_id", "=", "doctors.clinic_id")
                ->where('clinic_users.user_id', $this->userId)
                ->groupBy('doctor_reviews.id')
                ->select('doctor_reviews.*');

        } else if (auth()->user()->hasRole('customer')) {
            return $model->newQuery()->join("doctors", "doctors.id", "=", "doctor_reviews.doctor_id")
                ->join("doctor_appointments", "doctor_appointments.doctor_id", "=", "doctors.id")
                ->join("appointments", "doctor_appointments.appointment_id", "=", "appointments.id")
                ->where('appointments.user_id', $this->userId)
                ->groupBy('doctor_reviews.id')
                ->select('doctor_reviews.*');
        } else {
            return $model->select('doctor_reviews.*');
        }
    }
}

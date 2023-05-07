<?php
/*
 * File name: ClinicReviewsOfUserCriteria.php
 * Last modified: 2022.02.02 at 21:26:20
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Criteria\ClinicReviews;

use Illuminate\Support\Facades\Log;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ClinicReviewsOfUserCriteria.
 *
 * @package namespace App\Criteria\ClinicReviews;
 */
class ClinicReviewsOfUserCriteria implements CriteriaInterface
{
    /**
     * @var int
     */
    private $userId;

    /**
     * ClinicReviewsOfUserCriteria constructor.
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
            Log::info("admin Role");
            return $model->select('clinic_reviews.*');
        } else if (auth()->check() && auth()->user()->hasRole('clinic_owner')) {
            Log::info("clinic_owner Role");
            return $model
                ->join("clinic_users", "clinic_users.clinic_id", "=", "clinics.id")
                ->where('clinic_users.user_id', $this->userId)
                ->join("clinics", "clinics.id", "=", "clinic_reviews.clinic_id")
                ->groupBy('clinic_reviews.id')
                ->select('clinic_reviews.*');
        } else if (auth()->check() && auth()->user()->hasRole('customer')) {
            Log::info("customer Role");
            return $model->newQuery()->where('clinic_reviews.user_id', $this->userId)
                ->select('clinic_reviews.*');
        } else {
            return $model->select('clinic_reviews.*');
        }
    }
}

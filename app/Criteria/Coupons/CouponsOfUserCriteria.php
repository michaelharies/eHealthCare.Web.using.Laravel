<?php
/*
 * File name: CouponsOfUserCriteria.php
 * Last modified: 2021.01.25 at 19:19:41
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\Coupons;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class CouponsOfUserCriteria.
 *
 * @package namespace App\Criteria\Coupons;
 */
class CouponsOfUserCriteria implements CriteriaInterface
{
    /**
     * @var int
     */
    private $userId;

    /**
     * CouponsOfUserCriteria constructor.
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
        }elseif (auth()->user()->hasRole('clinic_owner')){
            $clinic = $model->join("discountables", "discountables.coupon_id", "=", "coupons.id")
                ->join("clinic_users", "clinic_users.clinic_id", "=", "discountables.discountable_id")
                ->where('discountable_type', 'App\\Models\\Clinic')
                ->where("clinic_users.user_id", $this->userId)
                ->select("coupons.*");

            return $model->join("discountables", "discountables.coupon_id", "=", "coupons.id")
                ->join("doctors", "doctors.id", "=", "discountables.discountable_id")
                ->where('discountable_type', 'App\\Models\\Doctor')
                ->join("clinic_users", "clinic_users.clinic_id", "=", "doctors.clinic_id")
                ->where("clinic_users.user_id", $this->userId)
                ->select("coupons.*")
                ->union($clinic);
        }else{
            return $model;
        }

    }
}

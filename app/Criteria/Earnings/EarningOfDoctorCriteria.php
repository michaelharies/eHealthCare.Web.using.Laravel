<?php
/*
 * File name: EarningOfDoctorCriteria.php
 * Last modified: 2021.02.22 at 10:53:38
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\Earnings;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class EarningOfClinicCriteriaCriteria.
 *
 * @package namespace App\Criteria\Earnings;
 */
class EarningOfDoctorCriteria implements CriteriaInterface
{
    private $clinic;

    /**
     * EarningOfClinicCriteriaCriteria constructor.
     */
    public function __construct($clinic)
    {
        $this->clinic = $clinic;
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
        return $model->where("clinic_id", $this->clinic);
    }
}

<?php
/*
 * File name: OptionsOfUserCriteria.php
 * Last modified: 2021.03.23 at 15:31:42
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\Options;

use App\Models\User;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class OptionsOfUserCriteria.
 *
 * @package namespace App\Criteria\Options;
 */
class OptionsOfUserCriteria implements CriteriaInterface
{

    /**
     * @var User
     */
    private $userId;

    /**
     * OptionsOfUserCriteria constructor.
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
            return $model->join('doctors', 'options.doctor_id', '=', 'doctors.id')
                ->join('clinic_users', 'clinic_users.clinic_id', '=', 'doctors.clinic_id')
                ->groupBy('options.id')
                ->select('options.*')
                ->where('clinic_users.user_id', $this->userId);
        } else {
            return $model;
        }
    }
}

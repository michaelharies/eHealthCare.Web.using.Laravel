<?php
/*
 * File name: AwardsOfUserCriteria.php
 * Last modified: 2021.03.23 at 11:47:29
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\Awards;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AwardsOfUserCriteria.
 *
 * @package namespace App\Criteria\Awards;
 */
class AwardsOfUserCriteria implements CriteriaInterface
{
    /**
     * @var int
     */
    private $userId;

    /**
     * AwardsOfUserCriteria constructor.
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
            return $model->join('clinic_users', 'clinic_users.clinic_id', '=', 'awards.clinic_id')
                ->groupBy('awards.id')
                ->select('awards.*')
                ->where('clinic_users.user_id', $this->userId);
        } else {
            return $model;
        }
    }
}

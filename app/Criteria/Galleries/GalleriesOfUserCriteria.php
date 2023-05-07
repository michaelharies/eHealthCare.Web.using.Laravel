<?php
/*
 * File name: GalleriesOfUserCriteria.php
 * Last modified: 2021.02.21 at 14:50:32
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\Galleries;

use App\Models\User;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class GalleriesOfUserCriteria.
 *
 * @package namespace App\Criteria\Galleries;
 */
class GalleriesOfUserCriteria implements CriteriaInterface
{
    /**
     * @var User
     */
    private $userId;

    /**
     * GalleriesOfUserCriteria constructor.
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
        } elseif (auth()->user()->hasRole('clinic_owner')) {
            return $model->join('clinic_users', 'clinic_users.clinic_id', '=', 'galleries.clinic_id')
                ->groupBy('galleries.id')
                ->select('galleries.*')
                ->where('clinic_users.user_id', $this->userId);
        } else {
            return $model;
        }
    }
}

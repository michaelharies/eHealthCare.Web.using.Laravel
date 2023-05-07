<?php
/*
 * File name: AvailableCriteria.php
 * Last modified: 2022.04.01 at 23:10:55
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Criteria\Doctors;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AvailableCriteria.
 *
 * @package namespace App\Criteria\Doctors;
 */
class AvailableCriteria implements CriteriaInterface
{
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
        return $model->where('doctors.available', '1');
    }
}

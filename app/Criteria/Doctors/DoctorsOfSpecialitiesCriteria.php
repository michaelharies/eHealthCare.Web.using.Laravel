<?php
/**
 * File name: DoctorsOfSpecialitiesCriteria.php
 * Last modified: 2021.01.02 at 19:09:36
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\Doctors;

use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class DoctorsOfSpecialitiesCriteria.
 *
 * @package namespace App\Criteria\Doctors;
 */
class DoctorsOfSpecialitiesCriteria implements CriteriaInterface

{
    /**
     * @var array
     */
    private $request;

    /**
     * DoctorsOfFieldsCriteria constructor.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
        if (!$this->request->has('specialities')) {
            return $model;
        } else {
            $specialities = $this->request->get('specialities');
            if (in_array('0', $specialities)) { // means all fields
                return $model;
            }
            return $model->whereIn('speciality_id', $this->request->get('specialities', []));
        }
    }
}

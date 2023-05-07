<?php
/**
 * File name: AppointmentsOfStatusesCriteria.php
 * Last modified: 2021.01.02 at 19:09:36
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\Appointments;

use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AppointmentsOfStatusesCriteria.
 *
 * @package namespace App\Criteria\Appointments;
 */
class AppointmentsOfStatusesCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    private $request;

    /**
     * AppointmentsOfStatusesCriteria constructor.
     * @param array $request
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
        if (!$this->request->has('statuses')) {
            return $model;
        } else {
            $statuses = $this->request->get('statuses');
            if (in_array('0', $statuses)) { // means all statuses
                return $model;
            }
            return $model->whereIn('appointment_status_id', $this->request->get('statuses', []));
        }
    }
}

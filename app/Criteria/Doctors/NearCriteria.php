<?php
/*
 * File name: NearCriteria.php
 * Last modified: 2021.03.25 at 16:41:38
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\Doctors;

use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class NearCriteria.
 *
 * @package namespace App\Criteria\Doctors;
 */
class NearCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    private $request;

    /**
     * NearCriteria constructor.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository){
        if ($this->request->has(['myLat', 'myLon', 'areaLat', 'areaLon'])) {
            $coordination = $this->request->only('myLat', 'myLon', 'areaLat', 'areaLon');
            $coordination = array_values($coordination);
            return $model->near(...$coordination);
        } else if ($this->request->has(['myLat', 'myLon'])) {
            $coordination = $this->request->only('myLat', 'myLon');
            $coordination = array_values($coordination);
            array_push($coordination, ...$coordination);

            return $model->near(...$coordination);
        } else {
            return $model->orderBy('available');
        }
    }
}

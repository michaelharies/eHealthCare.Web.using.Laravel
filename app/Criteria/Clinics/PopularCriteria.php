<?php
/*
 * File name: PopularCriteria.php
 * Last modified: 2022.02.02 at 21:31:35
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Criteria\Clinics;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class PopularCriteria.
 *
 * @package namespace App\Criteria\Clinics;
 */
class PopularCriteria implements CriteriaInterface
{
    // TODO Popular doctor
    /**
     * @var array
     */
    private $request;

    /**
     * PopularCriteria constructor.
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
    public function apply($model, RepositoryInterface $repository)
    {
        if ($this->request->has(['myLon', 'myLat', 'areaLon', 'areaLat'])) {

            $myLat = $this->request->get('myLat');
            $myLon = $this->request->get('myLon');
            $areaLat = $this->request->get('areaLat');
            $areaLon = $this->request->get('areaLon');

            return $model->select(DB::raw("SQRT(
                POW(69.1 * (latitude - $myLat), 2) +
                POW(69.1 * ($myLon - longitude) * COS(latitude / 57.3), 2)) AS distance, SQRT(
                POW(69.1 * (latitude - $areaLat), 2) +
                POW(69.1 * ($areaLon - longitude) * COS(latitude / 57.3), 2))  AS area count(clinics.id) as clinic_count"), "clinics.*")
                ->join('doctors', 'doctors.clinic_id', '=', 'clinics.id')
                ->join('doctor_appointments', 'doctors.id', '=', 'doctor_appointments.doctor_id')
                ->orderBy('clinic_count', 'desc')
                ->orderBy('area')
                ->groupBy('clinics.id');
        } else {
            return $model->select(DB::raw("count(clinics.id) as clinic_count"), "clinics.*")
                ->join('doctors', 'doctors.clinic_id', '=', 'clinics.id')
                ->join('doctor_appointments', 'doctors.id', '=', 'doctor_appointments.doctor_id')
                ->orderBy('clinic_count', 'desc')
                ->groupBy('clinics.id');
        }
    }
}

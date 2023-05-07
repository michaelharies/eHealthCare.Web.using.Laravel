<?php
/*
 * File name: TrendingWeekCriteria.php
 * Last modified: 2021.02.22 at 10:53:38
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\Doctors;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class TrendingWeekCriteria.
 *
 * @package namespace App\Criteria\Doctors;
 */
class TrendingWeekCriteria implements CriteriaInterface
{
    // TODO TrendingWeekCriteria
    /**
     * @var array
     */
    private $request;

    /**
     * TrendingWeekCriteria constructor.
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

            $myLat = $this->request->get('myLat', 0);
            $myLon = $this->request->get('myLon', 0);
            $areaLat = $this->request->get('areaLat', 0);
            $areaLon = $this->request->get('areaLon', 0);

            return $model->join('clinics', 'clinics.id', '=', 'doctors.clinic_id')->select(DB::raw("SQRT(
            POW(69.1 * (clinics.latitude - $myLat), 2) +
            POW(69.1 * ($myLon - clinics.longitude) * COS(clinics.latitude / 57.3), 2)) AS distance, SQRT(
            POW(69.1 * (clinics.latitude - $areaLat), 2) +
            POW(69.1 * ($areaLon - clinics.longitude) * COS(clinics.latitude / 57.3), 2)) AS area, count(doctors.id) as doctor_count"), 'doctors.*')
                ->join('doctor_appointments', 'doctors.id', '=', 'doctor_appointments.doctor_id')
                ->whereBetween('doctor_appointments.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->where('clinics.active', '1')
                ->orderBy('doctor_count', 'desc')
                ->orderBy('area')
                ->groupBy('doctors.id');
        } else {
            return $model->join('doctor_appointments', 'doctors.id', '=', 'doctor_appointments.doctor_id')
                ->join('clinics', 'clinics.id', '=', 'doctors.clinic_id')
                ->whereBetween('doctor_appointments.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->where('clinics.active', '1')
                ->groupBy('doctors.id')
                ->orderBy('doctor_count', 'desc')
                ->select('doctors.*', DB::raw('count(doctors.id) as doctor_count'));
        }
    }
}

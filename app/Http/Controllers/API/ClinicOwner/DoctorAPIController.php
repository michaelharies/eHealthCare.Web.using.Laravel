<?php
/*
 * File name: DoctorAPIController.php
 * Last modified: 2022.04.13 at 08:14:30
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\API\ClinicOwner;


use App\Criteria\Doctors\DoctorOfClinicCriteria;
use App\Criteria\Doctors\NearCriteria;
use App\Http\Controllers\Controller;
use App\Repositories\DoctorRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class DoctorController
 * @package App\Http\Controllers\API
 */
class DoctorAPIController extends Controller
{
    /** @var  doctorRepository */
    private $doctorRepository;

    public function __construct(DoctorRepository $doctorRepo)
    {
        parent::__construct();
        $this->doctorRepository = $doctorRepo;
    }

    /**
     * Display a listing of the Doctor.
     * GET|HEAD /doctors
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->doctorRepository->pushCriteria(new RequestCriteria($request));
            $this->doctorRepository->pushCriteria(new DoctorOfClinicCriteria(auth()->id()));
            $this->doctorRepository->pushCriteria(new NearCriteria($request));

            $doctors = $this->doctorRepository->all();

            if (!$request->has('all')) {
                $this->availableDoctors($doctors);
            }

            $this->orderByRating($request, $doctors);
            $this->limitOffset($request, $doctors);
            $this->filterCollection($request, $doctors);
            $doctors = array_values($doctors->toArray());

        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($doctors, 'Doctors retrieved successfully');
    }

    /**
     * @param Collection $doctors
     */
    private function availableDoctors(Collection &$doctors)
    {
        $doctors = $doctors->where('available', true);
    }

    /**
     * @param Request $request
     * @param Collection $doctors
     */
    private function orderByRating(Request $request, Collection &$doctors)
    {
        if ($request->has('rating')) {
            $doctors = $doctors->sortBy('rate', SORT_REGULAR, true);
        }
    }
}

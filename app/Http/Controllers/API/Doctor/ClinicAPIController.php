<?php
/*
 * File name: DoctorAPIController.php
 * Last modified: 2022.04.08 at 09:32:35
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\API\Doctor;


use App\Criteria\Clinics\ClinicsOfUserCriteria;
use App\Http\Controllers\Controller;
use App\Repositories\ClinicRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ClinicController
 * @package App\Http\Controllers\API
 */
class ClinicAPIController extends Controller
{
    /** @var  ClinicRepository */
    private $clinicRepository;

    public function __construct(ClinicRepository $clinicRepo)
    {
        $this->clinicRepository = $clinicRepo;
        parent::__construct();
    }

    /**
     * Display a listing of the Clinic.
     * GET|HEAD /clinics
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->clinicRepository->pushCriteria(new RequestCriteria($request));
            $this->clinicRepository->pushCriteria(new ClinicsOfUserCriteria(auth()->id()));
            $this->clinicRepository->pushCriteria(new LimitOffsetCriteria($request));
            $clinics = $this->clinicRepository->all();
            $this->filterCollection($request, $clinics);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($clinics->toArray(), 'E Providers retrieved successfully');
    }

    /**
     * Display the specified Clinic.
     * GET|HEAD /clinics/{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $this->clinicRepository->pushCriteria(new RequestCriteria($request));
            $this->clinicRepository->pushCriteria(new LimitOffsetCriteria($request));
            $clinic = $this->clinicRepository->findWithoutFail($id);
            if (empty($clinic)) {
                return $this->sendError('Clinic not found');
            }
            $this->filterModel($request, $clinic);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($clinic->toArray(), 'Clinic retrieved successfully');
    }
}

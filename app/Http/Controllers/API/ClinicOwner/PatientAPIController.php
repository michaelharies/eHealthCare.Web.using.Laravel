<?php
/*
 * File name: PatientAPIController.php
 * Last modified: 2022.04.13 at 08:14:30
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\API\ClinicOwner;


use App\Criteria\Patients\PatientOfClinicCriteria;
use App\Http\Controllers\Controller;
use App\Repositories\PatientRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class PatientController
 * @package App\Http\Controllers\API
 */
class PatientAPIController extends Controller
{
    /** @var  patientRepository */
    private $patientRepository;

    public function __construct(PatientRepository $patientRepo)
    {
        parent::__construct();
        $this->patientRepository = $patientRepo;
    }

    /**
     * Display a listing of the Patient.
     * GET|HEAD /patients
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->patientRepository->pushCriteria(new RequestCriteria($request));
            $this->patientRepository->pushCriteria(new PatientOfClinicCriteria(auth()->id()));

            $patients = $this->patientRepository->all();

            $patients = array_values($patients->toArray());

        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($patients, 'Patients retrieved successfully');
    }


}

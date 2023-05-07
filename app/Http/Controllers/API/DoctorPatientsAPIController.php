<?php

namespace App\Http\Controllers\API;


use App\Models\DoctorPatients;
use App\Repositories\DoctorPatientsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Response;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class DoctorPatientsController
 * @package App\Http\Controllers\API
 */

class DoctorPatientsAPIController extends Controller
{
    /** @var  DoctorPatientsRepository */
    private $doctorPatientsRepository;

    public function __construct(DoctorPatientsRepository $doctorPatientsRepo)
    {
        $this->doctorPatientsRepository = $doctorPatientsRepo;
    }

    /**
     * Display a listing of the DoctorPatients.
     * GET|HEAD /doctorPatients
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $this->doctorPatientsRepository->pushCriteria(new RequestCriteria($request));
            $this->doctorPatientsRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $doctorPatients = $this->doctorPatientsRepository->all();

        return $this->sendResponse($doctorPatients->toArray(), 'Doctor Patients retrieved successfully');
    }

    /**
     * Display the specified DoctorPatients.
     * GET|HEAD /doctorPatients/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var DoctorPatients $doctorPatients */
        if (!empty($this->doctorPatientsRepository)) {
            $doctorPatients = $this->doctorPatientsRepository->findWithoutFail($id);
        }

        if (empty($doctorPatients)) {
            return $this->sendError('Doctor Patients not found');
        }

        return $this->sendResponse($doctorPatients->toArray(), 'Doctor Patients retrieved successfully');
    }
}

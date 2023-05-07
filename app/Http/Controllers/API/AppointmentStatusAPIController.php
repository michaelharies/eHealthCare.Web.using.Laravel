<?php
/*
 * File name: AppointmentStatusAPIController.php
 * Last modified: 2021.02.12 at 11:06:02
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\AppointmentStatus;
use App\Repositories\AppointmentStatusRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class AppointmentStatusController
 * @package App\Http\Controllers\API
 */
class AppointmentStatusAPIController extends Controller
{
    /** @var  AppointmentStatusRepository */
    private $appointmentStatusRepository;

    public function __construct(AppointmentStatusRepository $appointmentStatusRepo)
    {
        $this->appointmentStatusRepository = $appointmentStatusRepo;
    }

    /**
     * Display a listing of the AppointmentStatus.
     * GET|HEAD /appointmentStatuses
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $this->appointmentStatusRepository->pushCriteria(new RequestCriteria($request));
            $this->appointmentStatusRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $appointmentStatuses = $this->appointmentStatusRepository->all();
        $this->filterCollection($request, $appointmentStatuses);

        return $this->sendResponse($appointmentStatuses->toArray(), 'Appointment Statuses retrieved successfully');
    }

    /**
     * Display the specified AppointmentStatus.
     * GET|HEAD /appointmentStatuses/{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        /** @var AppointmentStatus $appointmentStatus */
        if (!empty($this->appointmentStatusRepository)) {
            $appointmentStatus = $this->appointmentStatusRepository->findWithoutFail($id);
        }

        if (empty($appointmentStatus)) {
            return $this->sendError('Appointment Status not found');
        }

        return $this->sendResponse($appointmentStatus->toArray(), 'Appointment Status retrieved successfully');
    }
}

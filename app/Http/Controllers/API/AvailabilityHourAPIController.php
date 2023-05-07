<?php
/*
 * File name: AvailabilityHourAPIController.php
 * Last modified: 2022.04.13 at 10:05:52
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\API;


use App\Criteria\AvailabilityHours\AvailabilityHoursOfUserCriteria;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAvailabilityHourRequest;
use App\Http\Requests\UpdateAvailabilityHourRequest;
use App\Repositories\AvailabilityHourRepository;
use App\Repositories\DoctorRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class AvailabilityHourController
 * @package App\Http\Controllers\API
 */
class AvailabilityHourAPIController extends Controller
{
    /** @var  AvailabilityHourRepository */
    private $availabilityHourRepository;

    /** @var  DoctorRepository */
    private $doctorRepository;

    public function __construct(AvailabilityHourRepository $availabilityHourRepo, DoctorRepository $doctorRepo)
    {
        $this->availabilityHourRepository = $availabilityHourRepo;
        $this->doctorRepository = $doctorRepo;
        parent::__construct();
    }


    /**
     * Display a listing of the AvailabilityHour.
     * GET|HEAD /availabilityHours
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->availabilityHourRepository->pushCriteria(new RequestCriteria($request));
            $this->availabilityHourRepository->pushCriteria(new LimitOffsetCriteria($request));
            $availabilityHours = $this->availabilityHourRepository->all();
            $this->filterCollection($request, $availabilityHours);
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($availabilityHours->toArray(), 'Availability Hours retrieved successfully');
    }

    /**
     * Display the specified AvailabilityHour.
     * GET|HEAD /availabilityHours/{id}
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $this->doctorRepository->pushCriteria(new RequestCriteria($request));
            $this->doctorRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $doctor = $this->doctorRepository->findWithoutFail($id);
        if (empty($doctor)) {
            return $this->sendError('Doctor not found');
        }
        $calendar = [];
        $date = $request->input('date');
        if (!empty($date)) {
            $date = Carbon::createFromFormat('Y-m-d', $date);
            $calendar = $doctor->weekCalendar($date);
        }

        return $this->sendResponse($calendar, 'Availability Hours retrieved successfully');

    }

    /**
     * Store a newly created AvailabilityHour in storage.
     *
     * @param CreateAvailabilityHourRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateAvailabilityHourRequest $request): JsonResponse
    {
        $input = $request->all();
        try {
            $availabilityHour = $this->availabilityHourRepository->create($input);

        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($availabilityHour, __('lang.saved_successfully', ['operator' => __('lang.availability_hour')]));
    }

    /**
     * Update the specified AvailabilityHour in storage.
     *
     * @param int $id
     * @param UpdateAvailabilityHourRequest $request
     *
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function update(int $id, UpdateAvailabilityHourRequest $request): JsonResponse
    {
        $this->availabilityHourRepository->pushCriteria(new AvailabilityHoursOfUserCriteria(auth()->id()));
        $availabilityHour = $this->availabilityHourRepository->findWithoutFail($id);

        if (empty($availabilityHour)) {
            return $this->sendError(__('lang.not_found', ['operator' => __('lang.availability_hour')]));
        }
        $input = $request->all();
        try {
            $availabilityHour = $this->availabilityHourRepository->update($input, $id);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($availabilityHour, __('lang.updated_successfully', ['operator' => __('lang.availability_hour')]));
    }

    /**
     * Remove the specified AvailabilityHour from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->availabilityHourRepository->pushCriteria(new AvailabilityHoursOfUserCriteria(auth()->id()));
        $availabilityHour = $this->availabilityHourRepository->findWithoutFail($id);

        if (empty($availabilityHour)) {
            return $this->sendError(__('lang.not_found', ['operator' => __('lang.availability_hour')]));
        }

        $this->availabilityHourRepository->delete($id);
        return $this->sendResponse($availabilityHour, __('lang.deleted_successfully', ['operator' => __('lang.availability_hour')]));
    }
}

<?php
/*
 * File name: ClinicPayoutAPIController.php
 * Last modified: 2021.01.30 at 16:06:30
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\ClinicPayout;
use App\Repositories\ClinicPayoutRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class ClinicPayoutController
 * @package App\Http\Controllers\API
 */
class ClinicPayoutAPIController extends Controller
{
    /** @var  ClinicPayoutRepository */
    private $clinicPayoutRepository;

    public function __construct(ClinicPayoutRepository $clinicPayoutRepo)
    {
        $this->clinicPayoutRepository = $clinicPayoutRepo;
    }

    /**
     * Display a listing of the ClinicPayout.
     * GET|HEAD /clinicPayouts
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->clinicPayoutRepository->pushCriteria(new RequestCriteria($request));
            $this->clinicPayoutRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $clinicPayouts = $this->clinicPayoutRepository->all();

        return $this->sendResponse($clinicPayouts->toArray(), 'E Provider Payouts retrieved successfully');
    }

    /**
     * Display the specified ClinicPayout.
     * GET|HEAD /clinicPayouts/{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        /** @var ClinicPayout $clinicPayout */
        if (!empty($this->clinicPayoutRepository)) {
            $clinicPayout = $this->clinicPayoutRepository->findWithoutFail($id);
        }

        if (empty($clinicPayout)) {
            return $this->sendError('E Provider Payout not found');
        }

        return $this->sendResponse($clinicPayout->toArray(), 'E Provider Payout retrieved successfully');
    }
}

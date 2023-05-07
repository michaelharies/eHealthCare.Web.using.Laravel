<?php
/*
 * File name: ClinicLevelAPIController.php
 * Last modified: 2022.10.16 at 11:45:14
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\ClinicLevel;
use App\Repositories\ClinicLevelRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class ClinicLevelController
 * @package App\Http\Controllers\API
 */
class ClinicLevelAPIController extends Controller
{
    /** @var  ClinicLevelRepository */
    private $clinicLevelRepository;

    public function __construct(ClinicLevelRepository $clinicLevelRepo)
    {
        $this->clinicLevelRepository = $clinicLevelRepo;
        parent::__construct();
    }

    /**
     * Display a listing of the ClinicLevel.
     * GET|HEAD /clinicLevels
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->clinicLevelRepository->pushCriteria(new RequestCriteria($request));
            $this->clinicLevelRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $clinicLevels = $this->clinicLevelRepository->all();

        return $this->sendResponse($clinicLevels->toArray(), 'Clinic Levels retrieved successfully');
    }

    /**
     * Display the specified ClinicLevel.
     * GET|HEAD /clinicLevels/{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        /** @var ClinicLevel $clinicLevel */
        if (!empty($this->clinicLevelRepository)) {
            $clinicLevel = $this->clinicLevelRepository->findWithoutFail($id);
        }

        if (empty($clinicLevel)) {
            return $this->sendError('Clinic Level not found');
        }

        return $this->sendResponse($clinicLevel->toArray(), 'Clinic Level retrieved successfully');
    }
}

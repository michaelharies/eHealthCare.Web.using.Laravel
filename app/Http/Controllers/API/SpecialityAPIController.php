<?php
/*
 * File name: SpecialityAPIController.php
 * Last modified: 2021.03.24 at 21:33:26
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers\API;


use App\Criteria\Specialities\NearCriteria;
use App\Criteria\Specialities\ParentCriteria;
use App\Http\Controllers\Controller;
use App\Models\Speciality;
use App\Repositories\SpecialityRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class SpecialityController
 * @package App\Http\Controllers\API
 */
class SpecialityAPIController extends Controller
{
    /** @var  SpecialityRepository */
    private $specialityRepository;

    public function __construct(SpecialityRepository $specialityRepo)
    {
        $this->specialityRepository = $specialityRepo;
    }

    /**
     * Display a listing of the Speciality.
     * GET|HEAD /specialities
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $this->specialityRepository->pushCriteria(new RequestCriteria($request));
            $this->specialityRepository->pushCriteria(new ParentCriteria($request));
            $this->specialityRepository->pushCriteria(new NearCriteria($request));
            $this->specialityRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $specialities = $this->specialityRepository->all();

        return $this->sendResponse($specialities->toArray(), 'Specialities retrieved successfully');
    }

    /**
     * Display the specified Speciality.
     * GET|HEAD /specialities/{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        /** @var Speciality $speciality */
        if (!empty($this->specialityRepository)) {
            $speciality = $this->specialityRepository->findWithoutFail($id);
        }

        if (empty($speciality)) {
            return $this->sendError('Speciality not found');
        }

        return $this->sendResponse($speciality->toArray(), 'Speciality retrieved successfully');
    }
}

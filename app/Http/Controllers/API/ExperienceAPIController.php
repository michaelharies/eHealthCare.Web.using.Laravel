<?php
/*
 * File name: ExperienceAPIController.php
 * Last modified: 2022.04.07 at 11:05:25
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\API;


use App\Criteria\Experiences\ExperiencesOfUserCriteria;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateExperienceRequest;
use App\Http\Requests\UpdateExperienceRequest;
use App\Repositories\ExperienceRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class ExperienceController
 * @package App\Http\Controllers\API
 */
class ExperienceAPIController extends Controller
{
    /** @var  ExperienceRepository */
    private $experienceRepository;

    public function __construct(ExperienceRepository $experienceRepo)
    {
        $this->experienceRepository = $experienceRepo;
        parent::__construct();
    }

    /**
     * Display a listing of the Experience.
     * GET|HEAD /experiences
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->experienceRepository->pushCriteria(new RequestCriteria($request));
            $this->experienceRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $experiences = $this->experienceRepository->all();

        return $this->sendResponse($experiences->toArray(), 'Experiences retrieved successfully');
    }

    /**
     * Display the specified Experience.
     * GET|HEAD /experiences/{id}
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $this->experienceRepository->pushCriteria(new RequestCriteria($request));
            $this->experienceRepository->pushCriteria(new ExperiencesOfUserCriteria(auth()->id()));
            $this->experienceRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $experience = $this->experienceRepository->findWithoutFail($id);
        if (empty($experience)) {
            return $this->sendError(__('lang.not_found', ['operator' => __('lang.experience')]));
        }
        return $this->sendResponse($experience->toArray(), 'Experience retrieved successfully');
    }

    /**
     * Store a newly created Experience in storage.
     *
     * @param CreateExperienceRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateExperienceRequest $request): JsonResponse
    {
        $input = $request->all();
        try {
            $experience = $this->experienceRepository->create($input);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($experience->toArray(), __('lang.updated_successfully', ['operator' => __('lang.experience')]));

    }

    /**
     * Update the specified Experience in storage.
     *
     * @param int $id
     * @param UpdateExperienceRequest $request
     *
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function update(int $id, UpdateExperienceRequest $request): JsonResponse
    {
        $this->experienceRepository->pushCriteria(new ExperiencesOfUserCriteria(auth()->id()));
        $experience = $this->experienceRepository->findWithoutFail($id);

        if (empty($experience)) {
            return $this->sendError(__('lang.not_found', ['operator' => __('lang.experience')]));
        }
        $input = $request->all();
        try {
            $experience = $this->experienceRepository->update($input, $id);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($experience->toArray(), __('lang.updated_successfully', ['operator' => __('lang.experience')]));

    }


    /**
     * Remove the specified Experience from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->experienceRepository->pushCriteria(new ExperiencesOfUserCriteria(auth()->id()));
        $experience = $this->experienceRepository->findWithoutFail($id);
        if (empty($experience)) {
            return $this->sendError(__('lang.not_found', ['operator' => __('lang.experience')]));
        }
        $experience = $this->experienceRepository->delete($id);
        return $this->sendResponse($experience, __('lang.deleted_successfully', ['operator' => __('lang.experience')]));
    }
}

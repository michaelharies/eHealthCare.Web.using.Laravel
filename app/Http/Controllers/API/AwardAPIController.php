<?php
/*
 * File name: AwardAPIController.php
 * Last modified: 2022.04.07 at 11:29:57
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\API;


use App\Criteria\Awards\AwardsOfUserCriteria;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAwardRequest;
use App\Http\Requests\UpdateAwardRequest;
use App\Repositories\AwardRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class AwardController
 * @package App\Http\Controllers\API
 */
class AwardAPIController extends Controller
{
    /** @var  AwardRepository */
    private $awardRepository;

    public function __construct(AwardRepository $awardRepo)
    {
        $this->awardRepository = $awardRepo;
        parent::__construct();
    }

    /**
     * Display a listing of the Award.
     * GET|HEAD /awards
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->awardRepository->pushCriteria(new RequestCriteria($request));
            $this->awardRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $awards = $this->awardRepository->all();

        return $this->sendResponse($awards->toArray(), 'Awards retrieved successfully');
    }

    /**
     * Display the specified Award.
     * GET|HEAD /awards/{id}
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $this->awardRepository->pushCriteria(new RequestCriteria($request));
            $this->awardRepository->pushCriteria(new AwardsOfUserCriteria(auth()->id()));
            $this->awardRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $award = $this->awardRepository->findWithoutFail($id);
        if (empty($award)) {
            return $this->sendError(__('lang.not_found', ['operator' => __('lang.award')]));
        }
        return $this->sendResponse($award->toArray(), 'Award retrieved successfully');
    }

    /**
     * Store a newly created Award in storage.
     *
     * @param CreateAwardRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateAwardRequest $request): JsonResponse
    {
        $input = $request->all();
        try {
            $award = $this->awardRepository->create($input);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($award->toArray(), __('lang.updated_successfully', ['operator' => __('lang.award')]));

    }

    /**
     * Update the specified Award in storage.
     *
     * @param int $id
     * @param UpdateAwardRequest $request
     *
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function update(int $id, UpdateAwardRequest $request): JsonResponse
    {
        $this->awardRepository->pushCriteria(new AwardsOfUserCriteria(auth()->id()));
        $award = $this->awardRepository->findWithoutFail($id);

        if (empty($award)) {
            return $this->sendError(__('lang.not_found', ['operator' => __('lang.award')]));
        }
        $input = $request->all();
        try {
            $award = $this->awardRepository->update($input, $id);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($award->toArray(), __('lang.updated_successfully', ['operator' => __('lang.award')]));

    }


    /**
     * Remove the specified Award from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->awardRepository->pushCriteria(new AwardsOfUserCriteria(auth()->id()));
        $award = $this->awardRepository->findWithoutFail($id);
        if (empty($award)) {
            return $this->sendError(__('lang.not_found', ['operator' => __('lang.award')]));
        }
        $award = $this->awardRepository->delete($id);
        return $this->sendResponse($award, __('lang.deleted_successfully', ['operator' => __('lang.award')]));
    }
}

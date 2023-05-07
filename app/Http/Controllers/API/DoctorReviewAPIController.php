<?php
/*
 * File name: DoctorReviewAPIController.php
 * Last modified: 2021.02.22 at 10:53:38
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers\API;


use App\Criteria\DoctorReviews\DoctorReviewsOfUserCriteria;
use App\Http\Controllers\Controller;
use App\Repositories\DoctorReviewRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class DoctorReviewController
 * @package App\Http\Controllers\API
 */
class DoctorReviewAPIController extends Controller
{
    /** @var  DoctorReviewRepository */
    private $doctorReviewRepository;

    public function __construct(DoctorReviewRepository $doctorReviewRepo)
    {
        $this->doctorReviewRepository = $doctorReviewRepo;
    }

    /**
     * Display a listing of the DoctorReview.
     * GET|HEAD /doctorReviews
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->doctorReviewRepository->pushCriteria(new RequestCriteria($request));
            if (auth()->check()) {
                $this->doctorReviewRepository->pushCriteria(new DoctorReviewsOfUserCriteria(auth()->id()));
            }
            $this->doctorReviewRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $doctorReviews = $this->doctorReviewRepository->all();
        $this->filterCollection($request, $doctorReviews);

        return $this->sendResponse($doctorReviews->toArray(), 'Doctor Reviews retrieved successfully');
    }

    /**
     * Display the specified DoctorReview.
     * GET|HEAD /doctorReviews/{id}
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $this->doctorReviewRepository->pushCriteria(new RequestCriteria($request));
            $this->doctorReviewRepository->pushCriteria(new LimitOffsetCriteria($request));

        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $doctorReview = $this->doctorReviewRepository->findWithoutFail($id);
        if (empty($doctorReview)) {
            return $this->sendError(__('lang.not_found', ['operator' => __('lang.doctor_review')]));
        }
        $this->filterModel($request, $doctorReview);

        return $this->sendResponse($doctorReview->toArray(), 'Doctor Review retrieved successfully');
    }

    /**
     * Store a newly created Review in storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $uniqueInput = $request->only("user_id", "doctor_id");
        $otherInput = $request->except("user_id", "doctor_id");
        try {
            $review = $this->doctorReviewRepository->updateOrCreate($uniqueInput, $otherInput);
        } catch (ValidatorException $e) {
            return $this->sendError(__('lang.not_found', ['operator' => __('lang.doctor_review')]));
        }

        return $this->sendResponse($review->toArray(), __('lang.saved_successfully', ['operator' => __('lang.doctor_review')]));
    }
}

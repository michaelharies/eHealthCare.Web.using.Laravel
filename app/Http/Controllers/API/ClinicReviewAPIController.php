<?php

namespace App\Http\Controllers\API;


use App\Criteria\ClinicReviews\ClinicReviewsOfUserCriteria;
use App\Repositories\ClinicReviewRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class ClinicReviewController
 * @package App\Http\Controllers\API
 */

class ClinicReviewAPIController extends Controller
{
    /** @var  ClinicReviewRepository */
    private $clinicReviewRepository;

    public function __construct(ClinicReviewRepository $clinicReviewRepo)
    {
        $this->clinicReviewRepository = $clinicReviewRepo;
    }

    /**
     * Display a listing of the ClinicReview.
     * GET|HEAD /clinicReviews
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $this->clinicReviewRepository->pushCriteria(new RequestCriteria($request));
            if (auth()->check()) {
                $this->clinicReviewRepository->pushCriteria(new ClinicReviewsOfUserCriteria(auth()->id()));
            }
            $this->clinicReviewRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $clinicReviews = $this->clinicReviewRepository->all();
        $this->filterCollection($request, $clinicReviews);
        return $this->sendResponse($clinicReviews->toArray(), 'Clinic Reviews retrieved successfully');
    }

    /**
     * Display the specified ClinicReview.
     * GET|HEAD /clinicReviews/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id,Request $request)
    {
        try {
            $this->clinicReviewRepository->pushCriteria(new RequestCriteria($request));
            $this->clinicReviewRepository->pushCriteria(new LimitOffsetCriteria($request));

        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $clinicReview = $this->clinicReviewRepository->findWithoutFail($id);
        if (empty($clinicReview)) {
            return $this->sendError(__('lang.not_found', ['operator' => __('lang.clinic_review')]));
        }
        $this->filterModel($request, $clinicReview);

        return $this->sendResponse($clinicReview->toArray(), 'Clinic Review retrieved successfully');
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
        $uniqueInput = $request->only("user_id", "clinic_id");
        $otherInput = $request->except("user_id", "clinic_id");
        try {
            $review = $this->clinicReviewRepository->updateOrCreate($uniqueInput, $otherInput);
        } catch (ValidatorException $e) {
            return $this->sendError(__('lang.not_found', ['operator' => __('lang.clinic_review')]));
        }

        return $this->sendResponse($review->toArray(), __('lang.saved_successfully', ['operator' => __('lang.clinic_review')]));
    }
}

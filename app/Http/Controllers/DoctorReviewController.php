<?php
/*
 * File name: DoctorReviewController.php
 * Last modified: 2021.03.21 at 21:17:48
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers;

use App\Criteria\DoctorReviews\DoctorReviewsOfUserCriteria;
use App\DataTables\DoctorReviewDataTable;
use App\Http\Requests\CreateDoctorReviewRequest;
use App\Http\Requests\UpdateDoctorReviewRequest;
use App\Repositories\CustomFieldRepository;
use App\Repositories\DoctorRepository;
use App\Repositories\DoctorReviewRepository;
use App\Repositories\UserRepository;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

class DoctorReviewController extends Controller
{
    /** @var  DoctorReviewRepository */
    private $doctorReviewRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var DoctorRepository
     */
    private $doctorRepository;

    public function __construct(DoctorReviewRepository $doctorReviewRepo, CustomFieldRepository $customFieldRepo, UserRepository $userRepo
        , DoctorRepository $doctorRepo)
    {
        parent::__construct();
        $this->doctorReviewRepository = $doctorReviewRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->userRepository = $userRepo;
        $this->doctorRepository = $doctorRepo;
    }

    /**
     * Display a listing of the DoctorReview.
     *
     * @param DoctorReviewDataTable $doctorReviewDataTable
     * @return Response
     */
    public function index(DoctorReviewDataTable $doctorReviewDataTable)
    {
        return $doctorReviewDataTable->render('doctor_reviews.index');
    }

    /**
     * Show the form for creating a new DoctorReview.
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {
        $user = $this->userRepository->pluck('name', 'id');

        $doctor = $this->doctorRepository->pluck('name', 'id');


        $hasCustomField = in_array($this->doctorReviewRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->doctorReviewRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('doctor_reviews.create')->with("customFields", isset($html) ? $html : false)->with("user", $user)->with("doctor", $doctor);
    }

    /**
     * Store a newly created DoctorReview in storage.
     *
     * @param CreateDoctorReviewRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function store(CreateDoctorReviewRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->doctorReviewRepository->model());
        try {
            $doctorReview = $this->doctorReviewRepository->create($input);
            $doctorReview->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.doctor_review')]));

        return redirect(route('doctorReviews.index'));
    }

    /**
     * Display the specified DoctorReview.
     *
     * @param int $id
     *
     * @return Application|Factory|Response|View
     * @throws RepositoryException
     */
    public function show($id)
    {
        $this->doctorReviewRepository->pushCriteria(new DoctorReviewsOfUserCriteria(auth()->id()));
        $doctorReview = $this->doctorReviewRepository->findWithoutFail($id);

        if (empty($doctorReview)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.doctor_review')]));
            return redirect(route('doctorReviews.index'));
        }
        return view('doctor_reviews.show')->with('doctorReview', $doctorReview);
    }

    /**
     * Show the form for editing the specified DoctorReview.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function edit(int $id)
    {
        $this->doctorReviewRepository->pushCriteria(new DoctorReviewsOfUserCriteria(auth()->id()));
        $doctorReview = $this->doctorReviewRepository->findWithoutFail($id);
        if (empty($doctorReview)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.doctor_review')]));
            return redirect(route('doctorReviews.index'));
        }
        $user = $this->userRepository->pluck('name', 'id');

        $doctor = $this->doctorRepository->pluck('name', 'id');


        $customFieldsValues = $doctorReview->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->doctorReviewRepository->model());
        $hasCustomField = in_array($this->doctorReviewRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
        return view('doctor_reviews.edit')->with('doctorReview', $doctorReview)->with("customFields", isset($html) ? $html : false)->with("user", $user)->with("doctor", $doctor);
    }

    /**
     * Update the specified DoctorReview in storage.
     *
     * @param int $id
     * @param UpdateDoctorReviewRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function update(int $id, UpdateDoctorReviewRequest $request)
    {
        $this->doctorReviewRepository->pushCriteria(new DoctorReviewsOfUserCriteria(auth()->id()));
        $doctorReview = $this->doctorReviewRepository->findWithoutFail($id);

        if (empty($doctorReview)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.doctor_review')]));
            return redirect(route('doctorReviews.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->doctorReviewRepository->model());
        try {
            $doctorReview = $this->doctorReviewRepository->update($input, $id);


            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $doctorReview->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }
        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.doctor_review')]));
        return redirect(route('doctorReviews.index'));
    }

    /**
     * Remove the specified DoctorReview from storage.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function destroy(int $id)
    {
        $doctorReview = $this->doctorReviewRepository->findWithoutFail($id);

        if (empty($doctorReview)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.doctor_review')]));
            return redirect(route('doctorReviews.index'));
        }

        $this->doctorReviewRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.doctor_review')]));
        return redirect(route('doctorReviews.index'));
    }

    /**
     * Remove Media of DoctorReview
     * @param Request $request
     * @throws RepositoryException
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $this->doctorReviewRepository->pushCriteria(new DoctorReviewsOfUserCriteria(auth()->id()));
        $doctorReview = $this->doctorReviewRepository->findWithoutFail($input['id']);
        try {
            if ($doctorReview->hasMedia($input['collection'])) {
                $doctorReview->getFirstMedia($input['collection'])->delete();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

}

<?php

namespace App\Http\Controllers;

use App\DataTables\ClinicReviewDataTable;
use App\Http\Requests\CreateClinicReviewRequest;
use App\Http\Requests\UpdateClinicReviewRequest;
use App\Repositories\ClinicReviewRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\UserRepository;
                use App\Repositories\ClinicRepository;
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
use Prettus\Validator\Exceptions\ValidatorException;

class ClinicReviewController extends Controller
{
    /** @var  ClinicReviewRepository */
    private $clinicReviewRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
  * @var UserRepository
  */
private $userRepository;/**
  * @var ClinicRepository
  */
private $clinicRepository;

    public function __construct(ClinicReviewRepository $clinicReviewRepo, CustomFieldRepository $customFieldRepo , UserRepository $userRepo
                , ClinicRepository $clinicRepo)
    {
        parent::__construct();
        $this->clinicReviewRepository = $clinicReviewRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->userRepository = $userRepo;
                $this->clinicRepository = $clinicRepo;
    }

    /**
     * Display a listing of the ClinicReview.
     *
     * @param ClinicReviewDataTable $clinicReviewDataTable
     * @return Response
     */
    public function index(ClinicReviewDataTable $clinicReviewDataTable)
    {
        return $clinicReviewDataTable->render('clinic_reviews.index');
    }

    /**
     * Show the form for creating a new ClinicReview.
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {
        $user = $this->userRepository->pluck('name','id');

                $clinic = $this->clinicRepository->pluck('name','id');

        
        $hasCustomField = in_array($this->clinicReviewRepository->model(),setting('custom_field_models',[]));
            if($hasCustomField){
                $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->clinicReviewRepository->model());
                $html = generateCustomField($customFields);
            }
        return view('clinic_reviews.create')->with("customFields", isset($html) ? $html : false)->with("user",$user)->with("clinic",$clinic);
    }

    /**
     * Store a newly created ClinicReview in storage.
     *
     * @param CreateClinicReviewRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function store(CreateClinicReviewRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->clinicReviewRepository->model());
        try {
            $clinicReview = $this->clinicReviewRepository->create($input);
            $clinicReview->customFieldsValues()->createMany(getCustomFieldsValues($customFields,$request));
            
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully',['operator' => __('lang.clinic_review')]));

        return redirect(route('clinicReviews.index'));
    }

    /**
     * Display the specified ClinicReview.
     *
     * @param  int $id
     *
     * @return Application|Factory|Response|View
     */
    public function show($id)
    {
        $clinicReview = $this->clinicReviewRepository->findWithoutFail($id);

        if (empty($clinicReview)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.clinic_review')]));
            return redirect(route('clinicReviews.index'));
        }
        return view('clinic_reviews.show')->with('clinicReview', $clinicReview);
    }

    /**
     * Show the form for editing the specified ClinicReview.
     *
     * @param  int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function edit($id)
    {
        $clinicReview = $this->clinicReviewRepository->findWithoutFail($id);
        $user = $this->userRepository->pluck('name','id');

                $clinic = $this->clinicRepository->pluck('name','id');

        

        if (empty($clinicReview)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.clinic_review')]));

            return redirect(route('clinicReviews.index'));
        }
        $customFieldsValues = $clinicReview->customFieldsValues()->with('customField')->get();
        $customFields =  $this->customFieldRepository->findByField('custom_field_model', $this->clinicReviewRepository->model());
        $hasCustomField = in_array($this->clinicReviewRepository->model(),setting('custom_field_models',[]));
        if($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
        return view('clinic_reviews.edit')->with('clinicReview', $clinicReview)->with("customFields", isset($html) ? $html : false)->with("user",$user)->with("clinic",$clinic);
    }

    /**
     * Update the specified ClinicReview in storage.
     *
     * @param  int              $id
     * @param UpdateClinicReviewRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function update($id, UpdateClinicReviewRequest $request)
    {
        $clinicReview = $this->clinicReviewRepository->findWithoutFail($id);

        if (empty($clinicReview)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.clinic_review')]));
            return redirect(route('clinicReviews.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->clinicReviewRepository->model());
        try {
            $clinicReview = $this->clinicReviewRepository->update($input, $id);
            
            
            foreach (getCustomFieldsValues($customFields, $request) as $value){
                $clinicReview->customFieldsValues()
                    ->updateOrCreate(['custom_field_id'=>$value['custom_field_id']],$value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }
        Flash::success(__('lang.updated_successfully',['operator' => __('lang.clinic_review')]));
        return redirect(route('clinicReviews.index'));
    }

    /**
     * Remove the specified ClinicReview from storage.
     *
     * @param  int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function destroy($id)
    {
        $clinicReview = $this->clinicReviewRepository->findWithoutFail($id);

        if (empty($clinicReview)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.clinic_review')]));

            return redirect(route('clinicReviews.index'));
        }

        $this->clinicReviewRepository->delete($id);

        Flash::success(__('lang.deleted_successfully',['operator' => __('lang.clinic_review')]));
        return redirect(route('clinicReviews.index'));
    }

        /**
     * Remove Media of ClinicReview
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $clinicReview = $this->clinicReviewRepository->findWithoutFail($input['id']);
        try {
            if($clinicReview->hasMedia($input['collection'])){
                $clinicReview->getFirstMedia($input['collection'])->delete();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

}

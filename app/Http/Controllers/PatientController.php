<?php

namespace App\Http\Controllers;

use App\DataTables\PatientDataTable;
use App\Http\Requests\CreatePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Repositories\PatientRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\UserRepository;
use App\Repositories\UploadRepository;
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

class PatientController extends Controller
{
    /** @var  PatientRepository */
    private $patientRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
      * @var UserRepository
      */
    private $userRepository;
    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(PatientRepository $patientRepo, CustomFieldRepository $customFieldRepo , UserRepository $userRepo
                , UploadRepository $uploadRepo)
    {
        parent::__construct();
        $this->patientRepository = $patientRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->userRepository = $userRepo;
        $this->uploadRepository = $uploadRepo;
    }

    /**
     * Display a listing of the Patient.
     *
     * @param PatientDataTable $patientDataTable
     * @return Response
     */
    public function index(PatientDataTable $patientDataTable)
    {
        return $patientDataTable->render('patients.index');
    }


    /**
     * Show the form for creating a new Patient.
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {
        $user = $this->userRepository->pluck('name','id');

        
        $hasCustomField = in_array($this->patientRepository->model(),setting('custom_field_models',[]));
            if($hasCustomField){
                $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->patientRepository->model());
                $html = generateCustomField($customFields);
            }
        return view('patients.create')->with("customFields", isset($html) ? $html : false)->with("user",$user);
    }

    /**
     * Store a newly created Patient in storage.
     *
     * @param CreatePatientRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function store(CreatePatientRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->patientRepository->model());
        $patient = $this->patientRepository->create($input);
        $patient->customFieldsValues()->createMany(getCustomFieldsValues($customFields,$request));
        try {
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                foreach ($input['image'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($patient, 'image');
                }
            }
            if (isset($input['card_id']) && $input['card_id'] && is_array($input['card_id'])) {
                foreach ($input['card_id'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('card_id')->first();
                    $mediaItem->copy($patient, 'card_id');
                }
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully',['operator' => __('lang.patient')]));

        return redirect(route('patients.index'));
    }

    /**
     * Display the specified Patient.
     *
     * @param  int $id
     *
     * @return Application|Factory|Response|View
     */
    public function show($id)
    {
        $patient = $this->patientRepository->findWithoutFail($id);

        if (empty($patient)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.patient')]));
            return redirect(route('patients.index'));
        }
        return view('patients.show')->with('patient', $patient);
    }

    /**
     * Show the form for editing the specified Patient.
     *
     * @param  int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function edit($id)
    {
        $patient = $this->patientRepository->findWithoutFail($id);
        if (empty($patient)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.patient')]));

            return redirect(route('patients.index'));
        }
        $user = $this->userRepository->pluck('name','id');
        $customFieldsValues = $patient->customFieldsValues()->with('customField')->get();
        $customFields =  $this->customFieldRepository->findByField('custom_field_model', $this->patientRepository->model());
        $hasCustomField = in_array($this->patientRepository->model(),setting('custom_field_models',[]));
        if($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
        return view('patients.edit')->with('patient', $patient)->with("customFields", isset($html) ? $html : false)->with("user",$user);
    }

    /**
     * Update the specified Patient in storage.
     *
     * @param  int              $id
     * @param UpdatePatientRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function update($id, UpdatePatientRequest $request)
    {
        $patient = $this->patientRepository->findWithoutFail($id);

        if (empty($patient)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.patient')]));
            return redirect(route('patients.index'));
        }
        $input = $request->all();

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->patientRepository->model());
        try {
            //dd($input);
            $patient = $this->patientRepository->update($input, $id);
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                foreach ($input['image'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($patient, 'image');
                }
            }
            if (isset($input['card_id']) && $input['card_id'] && is_array($input['card_id'])) {
                foreach ($input['card_id'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('card_id')->first();
                    $mediaItem->copy($patient, 'card_id');
                }
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value){
                $patient->customFieldsValues()
                    ->updateOrCreate(['custom_field_id'=>$value['custom_field_id']],$value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }
        Flash::success(__('lang.updated_successfully',['operator' => __('lang.patient')]));
        return redirect(route('patients.index'));
    }

    /**
     * Remove the specified Patient from storage.
     *
     * @param  int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function destroy($id)
    {
        $patient = $this->patientRepository->findWithoutFail($id);

        if (empty($patient)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.patient')]));

            return redirect(route('patients.index'));
        }

        $this->patientRepository->delete($id);

        Flash::success(__('lang.deleted_successfully',['operator' => __('lang.patient')]));
        return redirect(route('patients.index'));
    }

        /**
     * Remove Media of Patient
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $patient = $this->patientRepository->findWithoutFail($input['id']);
        try {
            if ($patient->hasMedia($input['collection'])) {
                $patient->getFirstMedia($input['collection'])->delete();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

}

<?php

namespace App\Http\Controllers;

use App\DataTables\DoctorPatientsDataTable;
use App\Http\Requests\CreateDoctorPatientsRequest;
use App\Http\Requests\UpdateDoctorPatientsRequest;
use App\Repositories\DoctorPatientsRepository;
use App\Repositories\CustomFieldRepository;

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

class DoctorPatientsController extends Controller
{
    /** @var  DoctorPatientsRepository */
    private $doctorPatientsRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    

    public function __construct(DoctorPatientsRepository $doctorPatientsRepo, CustomFieldRepository $customFieldRepo )
    {
        parent::__construct();
        $this->doctorPatientsRepository = $doctorPatientsRepo;
        $this->customFieldRepository = $customFieldRepo;
        
    }

    /**
     * Display a listing of the DoctorPatients.
     *
     * @param DoctorPatientsDataTable $doctorPatientsDataTable
     * @return Response
     */
    public function index(DoctorPatientsDataTable $doctorPatientsDataTable)
    {
        return $doctorPatientsDataTable->render('doctor_patients.index');
    }

    /**
     * Show the form for creating a new DoctorPatients.
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {
        
        
        $hasCustomField = in_array($this->doctorPatientsRepository->model(),setting('custom_field_models',[]));
            if($hasCustomField){
                $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->doctorPatientsRepository->model());
                $html = generateCustomField($customFields);
            }
        return view('doctor_patients.create')->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Store a newly created DoctorPatients in storage.
     *
     * @param CreateDoctorPatientsRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function store(CreateDoctorPatientsRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->doctorPatientsRepository->model());
        try {
            $doctorPatients = $this->doctorPatientsRepository->create($input);
            $doctorPatients->customFieldsValues()->createMany(getCustomFieldsValues($customFields,$request));
            
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully',['operator' => __('lang.doctor_patients')]));

        return redirect(route('doctorPatients.index'));
    }

    /**
     * Display the specified DoctorPatients.
     *
     * @param  int $id
     *
     * @return Application|Factory|Response|View
     */
    public function show($id)
    {
        $doctorPatients = $this->doctorPatientsRepository->findWithoutFail($id);

        if (empty($doctorPatients)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.doctor_patients')]));
            return redirect(route('doctorPatients.index'));
        }
        return view('doctor_patients.show')->with('doctorPatients', $doctorPatients);
    }

    /**
     * Show the form for editing the specified DoctorPatients.
     *
     * @param  int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function edit($id)
    {
        $doctorPatients = $this->doctorPatientsRepository->findWithoutFail($id);
        
        

        if (empty($doctorPatients)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.doctor_patients')]));

            return redirect(route('doctorPatients.index'));
        }
        $customFieldsValues = $doctorPatients->customFieldsValues()->with('customField')->get();
        $customFields =  $this->customFieldRepository->findByField('custom_field_model', $this->doctorPatientsRepository->model());
        $hasCustomField = in_array($this->doctorPatientsRepository->model(),setting('custom_field_models',[]));
        if($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
        return view('doctor_patients.edit')->with('doctorPatients', $doctorPatients)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified DoctorPatients in storage.
     *
     * @param  int              $id
     * @param UpdateDoctorPatientsRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function update($id, UpdateDoctorPatientsRequest $request)
    {
        $doctorPatients = $this->doctorPatientsRepository->findWithoutFail($id);

        if (empty($doctorPatients)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.doctor_patients')]));
            return redirect(route('doctorPatients.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->doctorPatientsRepository->model());
        try {
            $doctorPatients = $this->doctorPatientsRepository->update($input, $id);
            
            
            foreach (getCustomFieldsValues($customFields, $request) as $value){
                $doctorPatients->customFieldsValues()
                    ->updateOrCreate(['custom_field_id'=>$value['custom_field_id']],$value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }
        Flash::success(__('lang.updated_successfully',['operator' => __('lang.doctor_patients')]));
        return redirect(route('doctorPatients.index'));
    }

    /**
     * Remove the specified DoctorPatients from storage.
     *
     * @param  int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function destroy($id)
    {
        $doctorPatients = $this->doctorPatientsRepository->findWithoutFail($id);

        if (empty($doctorPatients)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.doctor_patients')]));

            return redirect(route('doctorPatients.index'));
        }

        $this->doctorPatientsRepository->delete($id);

        Flash::success(__('lang.deleted_successfully',['operator' => __('lang.doctor_patients')]));
        return redirect(route('doctorPatients.index'));
    }

        /**
     * Remove Media of DoctorPatients
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $doctorPatients = $this->doctorPatientsRepository->findWithoutFail($input['id']);
        try {
            if($doctorPatients->hasMedia($input['collection'])){
                $doctorPatients->getFirstMedia($input['collection'])->delete();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

}

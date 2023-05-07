<?php
/*
 * File name: DoctorController.php
 * Last modified: 2021.03.21 at 15:11:01
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers;

use App\Criteria\Clinics\ClinicsOfUserCriteria;
use App\Criteria\Doctors\DoctorsOfUserCriteria;
use App\DataTables\DoctorDataTable;
use App\Http\Requests\CreateDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Repositories\SpecialityRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\DoctorRepository;
use App\Repositories\UploadRepository;
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

class DoctorController extends Controller
{
    /** @var  DoctorRepository */
    private $doctorRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;
    /**
     * @var SpecialityRepository
     */
    private $specialityRepository;
    /**
     * @var ClinicRepository
     */
    private $clinicRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(DoctorRepository $doctorRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo
        , SpecialityRepository $specialityRepo
        , ClinicRepository $clinicRepo
        ,UserRepository $userRepo)

    {
        parent::__construct();
        $this->doctorRepository = $doctorRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
        $this->specialityRepository = $specialityRepo;
        $this->clinicRepository = $clinicRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the Doctor.
     *
     * @param DoctorDataTable $doctorDataTable
     * @return Response
     */
    public function index(DoctorDataTable $doctorDataTable)
    {
        return $doctorDataTable->render('doctors.index');
    }

    /**
     * Show the form for creating a new Doctor.
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {
        $user = $this->userRepository->pluck('name','id');
        $speciality = $this->specialityRepository->pluck('name', 'id');
        $clinic = $this->clinicRepository->getByCriteria(new ClinicsOfUserCriteria(auth()->id()))->pluck('name', 'id');
        $specialitiesSelected = [];
        $hasCustomField = in_array($this->doctorRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->doctorRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('doctors.create')->with("customFields", isset($html) ? $html : false)->with("speciality", $speciality)->with("specialitiesSelected", $specialitiesSelected)->with("clinic", $clinic)->with("user",$user);
    }

    /**
     * Store a newly created Doctor in storage.
     *
     * @param CreateDoctorRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function store(CreateDoctorRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->doctorRepository->model());
        try {
            $doctor = $this->doctorRepository->create($input);
            $doctor->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                foreach ($input['image'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($doctor, 'image');
                }
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.doctor')]));

        return redirect(route('doctors.index'));
    }

    /**
     * Display the specified Doctor.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function show(int $id)
    {
        $this->doctorRepository->pushCriteria(new DoctorsOfUserCriteria(auth()->id()));
        $doctor = $this->doctorRepository->findWithoutFail($id);

        if (empty($doctor)) {
            Flash::error('Doctor not found');

            return redirect(route('doctors.index'));
        }

        return view('doctors.show')->with('doctor', $doctor);
    }

    /**
     * Show the form for editing the specified Doctor.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function edit(int $id)
    {
        $user = $this->userRepository->pluck('name','id');
        $this->doctorRepository->pushCriteria(new DoctorsOfUserCriteria(auth()->id()));
        $doctor = $this->doctorRepository->findWithoutFail($id);
        if (empty($doctor)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.doctor')]));

            return redirect(route('doctors.index'));
        }
        $speciality = $this->specialityRepository->pluck('name', 'id');
        $clinic = $this->clinicRepository->getByCriteria(new ClinicsOfUserCriteria(auth()->id()))->pluck('name', 'id');
        $specialitiesSelected = $doctor->specialities()->pluck('specialities.id')->toArray();

        $customFieldsValues = $doctor->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->doctorRepository->model());
        $hasCustomField = in_array($this->doctorRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('doctors.edit')->with('doctor', $doctor)->with("customFields", isset($html) ? $html : false)->with("speciality", $speciality)->with("specialitiesSelected", $specialitiesSelected)->with("clinic", $clinic)->with("user",$user);
    }

    /**
     * Update the specified Doctor in storage.
     *
     * @param int $id
     * @param UpdateDoctorRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function update(int $id, UpdateDoctorRequest $request)
    {
        $this->doctorRepository->pushCriteria(new DoctorsOfUserCriteria(auth()->id()));
        $doctor = $this->doctorRepository->findWithoutFail($id);

        if (empty($doctor)) {
            Flash::error('Doctor not found');
            return redirect(route('doctors.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->doctorRepository->model());
        try {
            $input['specialities'] = isset($input['specialities']) ? $input['specialities'] : [];
            $doctor = $this->doctorRepository->update($input, $id);
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                foreach ($input['image'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($doctor, 'image');
                }
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $doctor->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.doctor')]));

        return redirect(route('doctors.index'));
    }

    /**
     * Remove the specified Doctor from storage.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function destroy(int $id)
    {
        $this->doctorRepository->pushCriteria(new DoctorsOfUserCriteria(auth()->id()));
        $doctor = $this->doctorRepository->findWithoutFail($id);

        if (empty($doctor)) {
            Flash::error('E Service not found');

            return redirect(route('doctors.index'));
        }

        $this->doctorRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.doctor')]));

        return redirect(route('doctors.index'));
    }

    /**
     * Remove Media of Doctor
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $doctor = $this->doctorRepository->findWithoutFail($input['id']);
        try {
            if ($doctor->hasMedia($input['collection'])) {
                $doctor->getFirstMedia($input['collection'])->delete();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}

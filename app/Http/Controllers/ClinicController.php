<?php
/*
 * File name: ClinicController.php
 * Last modified: 2021.11.04 at 11:59:07
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers;

use App\Criteria\Addresses\AddressesOfUserCriteria;
use App\Criteria\ClinicLevels\EnabledCriteria;
use App\Criteria\Clinics\ClinicsOfUserCriteria;
use App\Criteria\Users\ClinicsCustomersCriteria;
use App\DataTables\ClinicDataTable;
use App\DataTables\RequestedClinicDataTable;
use App\Events\ClinicChangedEvent;
use App\Http\Requests\CreateClinicRequest;
use App\Http\Requests\UpdateClinicRequest;
use App\Repositories\AddressRepository;
use App\Repositories\ClinicLevelRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\SalonLevelRepository;
use App\Repositories\TaxRepository;
use App\Repositories\UploadRepository;
use App\Repositories\UserRepository;
use Exception;
use Laracasts\Flash\Flash;
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
use function MongoDB\BSON\toJSON;

class ClinicController extends Controller
{
    /** @var  ClinicRepository */
    private $clinicRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;
    /**
     * @var ClinicLevelRepository
     */
    private $clinicLevelRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var AddressRepository
     */
    private $addressRepository;
    /**
     * @var TaxRepository
     */
    private $taxRepository;

    public function __construct(
        ClinicRepository $clinicRepo,
        CustomFieldRepository $customFieldRepo,
        UploadRepository $uploadRepo,
        UserRepository $userRepo,
        AddressRepository $addressRepo,
        TaxRepository $taxRepo,
        ClinicLevelRepository $clinicLevelRepo)
    {
        parent::__construct();
        $this->clinicRepository = $clinicRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
        $this->userRepository = $userRepo;
        $this->addressRepository = $addressRepo;
        $this->taxRepository = $taxRepo;
        $this->clinicLevelRepository = $clinicLevelRepo;
    }

    /**
     * Display a listing of the Clinic.
     *
     * @param ClinicDataTable $clinicDataTable
     * @return mixed
     */
    public function index(ClinicDataTable $clinicDataTable)
    {
       return $clinicDataTable->render('clinics.index');
    }

    /**
     * Display a listing of the Clinic.
     *
     * @param RequestedClinicDataTable $requestedClinicDataTable
     * @return mixed
     */
    public function requestedClinics(RequestedClinicDataTable $requestedClinicDataTable)
    {
        return $requestedClinicDataTable->render('clinics.requested');
    }

    /**
     * Show the form for creating a new Clinic.
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {

        $clinicLevel = $this->clinicLevelRepository->getByCriteria(new EnabledCriteria())->pluck('name', 'id');
        $user = $this->userRepository->getByCriteria(new ClinicsCustomersCriteria())->pluck('name', 'id');
        $address = $this->addressRepository->getByCriteria(new AddressesOfUserCriteria(auth()->id()))->pluck('address', 'id');
        $tax = $this->taxRepository->pluck('name', 'id');
        $addressesSelected = [];
        $taxesSelected = [];
        $usersSelected = [];
        $hasCustomField = in_array($this->clinicRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->clinicRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('clinics.create')->with("customFields", isset($html) ? $html : false)->with("user", $user)->with("clinicLevel", $clinicLevel)->with("address", $address)->with("addressesSelected", $addressesSelected)->with("tax", $tax)->with("taxesSelected", $taxesSelected)->with("usersSelected", $usersSelected);

    }

    /**
     * Store a newly created Clinic in storage.
     *
     * @param CreateClinicRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function store(CreateClinicRequest $request)
    {
        $input = $request->all();
        if (auth()->user()->hasRole(['clinic_owner', 'customer'])) {
            $input['users'] = [auth()->id()];
        }
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->clinicRepository->model());
        try {
            $clinic = $this->clinicRepository->create($input);
            $clinic->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                foreach ($input['image'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($clinic, 'image');
                }
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.clinic')]));

        return redirect(route('clinics.index'));
    }

    /**
     * Display the specified Clinic.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function show(int $id)
    {
        $this->clinicRepository->pushCriteria(new ClinicsOfUserCriteria(auth()->id()));
        $clinic = $this->clinicRepository->findWithoutFail($id);

        if (empty($clinic)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.clinic')]));

            return redirect(route('clinics.index'));
        }

        return view('clinics.show')->with('clinic', $clinic);
    }

    /**
     * Show the form for editing the specified Clinic.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function edit(int $id)
    {

        $this->clinicRepository->pushCriteria(new ClinicsOfUserCriteria(auth()->id()));
        $clinic = $this->clinicRepository->findWithoutFail($id);
        if (empty($clinic)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.clinic')]));
            return redirect(route('clinics.index'));
        }
        $clinicLevel = $this->clinicLevelRepository->getByCriteria(new EnabledCriteria())->pluck('name', 'id');
        $user = $this->userRepository->getByCriteria(new ClinicsCustomersCriteria())->pluck('name', 'id');
        $address = $this->addressRepository->getByCriteria(new AddressesOfUserCriteria(auth()->id()))->pluck('address', 'id');
        $tax = $this->taxRepository->pluck('name', 'id');
        $taxesSelected = $clinic->taxes()->pluck('taxes.id')->toArray();
        $usersSelected = $clinic->users()->pluck('users.id')->toArray();

        $customFieldsValues = $clinic->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->clinicRepository->model());
        $hasCustomField = in_array($this->clinicRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('clinics.edit')->with('clinic', $clinic)->with("customFields", isset($html) ? $html : false)->with("user", $user)->with("clinicLevel", $clinicLevel)->with("address", $address)->with("tax", $tax)->with("taxesSelected", $taxesSelected)->with("usersSelected", $usersSelected);
    }

    /**
     * Update the specified Clinic in storage.
     *
     * @param int $id
     * @param UpdateClinicRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function update(int $id, UpdateClinicRequest $request)
    {
        $this->clinicRepository->pushCriteria(new ClinicsOfUserCriteria(auth()->id()));
        $oldClinic = $this->clinicRepository->findWithoutFail($id);

        if (empty($oldClinic)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.clinic')]));
            return redirect(route('clinics.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->clinicRepository->model());
        try {
            $input['addresses'] = isset($input['addresses']) ? $input['addresses'] : [];
            $input['taxes'] = isset($input['taxes']) ? $input['taxes'] : [];
            $clinic = $this->clinicRepository->update($input, $id);
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                foreach ($input['image'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($clinic, 'image');
                }
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $clinic->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
            event(new ClinicChangedEvent($clinic, $oldClinic));
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.clinic')]));

        return redirect(route('clinics.index'));
    }

    /**
     * Remove the specified Clinic from storage.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function destroy(int $id)
    {
        if (config('installer.demo_app')) {
            Flash::warning('This is only demo app you can\'t change this section ');
            return redirect(route('clinics.index'));
        }
        $this->clinicRepository->pushCriteria(new ClinicsOfUserCriteria(auth()->id()));
        $clinic = $this->clinicRepository->findWithoutFail($id);

        if (empty($clinic)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.clinic')]));

            return redirect(route('clinics.index'));
        }

        $this->clinicRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.clinic')]));

        return redirect(route('clinics.index'));
    }

    /**
     * Remove Media of Clinic
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $clinic = $this->clinicRepository->findWithoutFail($input['id']);
        try {
            if ($clinic->hasMedia($input['collection'])) {
                $clinic->getFirstMedia($input['collection'])->delete();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}

<?php
/*
 * File name: CouponController.php
 * Last modified: 2021.02.05 at 10:52:12
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers;

use App\Criteria\Coupons\CouponsOfUserCriteria;
use App\Criteria\Clinics\AcceptedCriteria;
use App\Criteria\Clinics\ClinicsOfUserCriteria;
use App\Criteria\Doctors\DoctorsOfUserCriteria;
use App\DataTables\CouponDataTable;
use App\Http\Requests\CreateCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Repositories\SpecialityRepository;
use App\Repositories\CouponRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\DiscountableRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\DoctorRepository;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

class CouponController extends Controller
{
    /** @var  CouponRepository */
    private $couponRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var DoctorRepository
     */
    private $doctorRepository;
    /**
     * @var ClinicRepository
     */
    private $clinicRepository;
    /**
     * @var SpecialityRepository
     */
    private $specialityRepository;
    /**
     * @var DiscountableRepository
     */
    private $discountableRepository;

    public function __construct(CouponRepository $couponRepo, CustomFieldRepository $customFieldRepo, DoctorRepository $doctorRepo
        , ClinicRepository $clinicRepo
        , SpecialityRepository $specialityRepo, DiscountableRepository $discountableRepository)
    {
        parent::__construct();
        $this->couponRepository = $couponRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->doctorRepository = $doctorRepo;
        $this->clinicRepository = $clinicRepo;
        $this->specialityRepository = $specialityRepo;
        $this->discountableRepository = $discountableRepository;
    }

    /**
     * Display a listing of the Coupon.
     *
     * @param CouponDataTable $couponDataTable
     * @return Response
     */
    public function index(CouponDataTable $couponDataTable)
    {
        return $couponDataTable->render('coupons.index');
    }

    /**
     * Show the form for creating a new Coupon.
     *
     * @return Application|Factory|Response|View
     * @throws RepositoryException
     */
    public function create()
    {
        $this->doctorRepository->pushCriteria(new DoctorsOfUserCriteria(auth()->id()));
        $doctor = $this->doctorRepository->groupedByClinics();

        $this->clinicRepository->pushCriteria(new ClinicsOfUserCriteria(auth()->id()));
        $this->clinicRepository->pushCriteria(new AcceptedCriteria());
        $clinic = $this->clinicRepository->pluck('name', 'id');

        $speciality = $this->specialityRepository->pluck('name', 'id');

        $doctorsSelected = [];
        $clinicsSelected = [];
        $specialitiesSelected = [];

        $hasCustomField = in_array($this->couponRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->couponRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('coupons.create')->with("customFields", isset($html) ? $html : false)->with("doctor", $doctor)->with("clinic", $clinic)->with("speciality", $speciality)->with("doctorsSelected", $doctorsSelected)->with("clinicsSelected", $clinicsSelected)->with("specialitiesSelected", $specialitiesSelected);
    }

    /**
     * Store a newly created Coupon in storage.
     *
     * @param CreateCouponRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function store(CreateCouponRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->couponRepository->model());
        try {
            $coupon = $this->couponRepository->create($input);
            $discountables = $this->initDiscountables($input);
            $coupon->discountables()->createMany($discountables);
            $coupon->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.coupon')]));

        return redirect(route('coupons.index'));
    }

    /**
     * @param array $input
     * @return array
     */
    private function initDiscountables(array $input): array
    {
        $discountables = [];
        if (isset($input['doctors'])) {
            foreach ($input['doctors'] as $doctorId) {
                $discountables[] = ["discountable_type" => "App\Models\Doctor", "discountable_id" => $doctorId];
            }
        }
        if (isset($input['clinics'])) {
            foreach ($input['clinics'] as $clinicId) {
                $discountables[] = ["discountable_type" => "App\Models\Clinic", "discountable_id" => $clinicId];
            }
        }
        if (isset($input['specialities'])) {
            foreach ($input['specialities'] as $specialityId) {
                $discountables[] = ["discountable_type" => "App\Models\Speciality", "discountable_id" => $specialityId];
            }
        }
        return $discountables;
    }

    /**
     * Display the specified Coupon.
     *
     * @param int $id
     *
     * @return Application|Factory|Response|View
     */
    public function show(int $id)
    {
        $coupon = $this->couponRepository->findWithoutFail($id);

        if (empty($coupon)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.coupon')]));

            return redirect(route('coupons.index'));
        }

        return view('coupons.show')->with('coupon', $coupon);
    }

    /**
     * Show the form for editing the specified Coupon.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function edit(int $id)
    {
        $this->couponRepository->pushCriteria(new CouponsOfUserCriteria(auth()->id()));

        $coupon = $this->couponRepository->all()->firstWhere('id', '=', $id);

        if (empty($coupon)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.coupon')]));

            return redirect(route('coupons.index'));
        }
        $this->doctorRepository->pushCriteria(new DoctorsOfUserCriteria(auth()->id()));
        $doctor = $this->doctorRepository->groupedByClinics();

        $this->clinicRepository->pushCriteria(new ClinicsOfUserCriteria(auth()->id()));
        $this->clinicRepository->pushCriteria(new AcceptedCriteria());
        $clinic = $this->clinicRepository->pluck('name', 'id');

        $speciality = $this->specialityRepository->pluck('name', 'id');

        $doctorsSelected = $coupon->discountables()->where("discountable_type", "App\Models\Doctor")->pluck('discountable_id');
        $clinicsSelected = $coupon->discountables()->where("discountable_type", "App\Models\Clinic")->pluck('discountable_id');
        $specialitiesSelected = $coupon->discountables()->where("discountable_type", "App\Models\Speciality")->pluck('discountable_id');

        $customFieldsValues = $coupon->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->couponRepository->model());
        $hasCustomField = in_array($this->couponRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('coupons.edit')->with('coupon', $coupon)->with("customFields", isset($html) ? $html : false)->with("doctor", $doctor)->with("clinic", $clinic)->with("speciality", $speciality)->with("doctorsSelected", $doctorsSelected)->with("clinicsSelected", $clinicsSelected)->with("specialitiesSelected", $specialitiesSelected);
    }

    /**
     * Update the specified Coupon in storage.
     *
     * @param int $id
     * @param UpdateCouponRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function update(int $id, UpdateCouponRequest $request)
    {
        $this->couponRepository->pushCriteria(new CouponsOfUserCriteria(auth()->id()));

        $coupon = $this->couponRepository->all()->firstWhere('id', '=', $id);

        if (empty($coupon)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.coupon')]));
            return redirect(route('coupons.index'));
        }
        $input = $request->all();
        unset($input['code']);
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->couponRepository->model());
        try {
            $coupon = $this->couponRepository->update($input, $id);
            $discountables = $this->initDiscountables($input);
            $coupon->discountables()->delete();
            $coupon->discountables()->createMany($discountables);


            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $coupon->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.coupon')]));

        return redirect(route('coupons.index'));
    }

    /**
     * Remove the specified Coupon from storage.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function destroy(int $id)
    {
        $coupon = $this->couponRepository->findWithoutFail($id);

        if (empty($coupon)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.coupon')]));

            return redirect(route('coupons.index'));
        }

        $this->couponRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.coupon')]));

        return redirect(route('coupons.index'));
    }
}

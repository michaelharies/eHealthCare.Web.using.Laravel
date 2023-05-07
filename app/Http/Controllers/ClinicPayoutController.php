<?php
/*
 * File name: ClinicPayoutController.php
 * Last modified: 2021.03.25 at 16:41:38
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers;

use App\Criteria\Clinics\ClinicsOfUserCriteria;
use App\DataTables\ClinicPayoutDataTable;
use App\Http\Requests\CreateClinicPayoutRequest;
use App\Repositories\CustomFieldRepository;
use App\Repositories\EarningRepository;
use App\Repositories\ClinicPayoutRepository;
use App\Repositories\ClinicRepository;
use Carbon\Carbon;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

class ClinicPayoutController extends Controller
{
    /** @var  ClinicPayoutRepository */
    private $clinicPayoutRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var ClinicRepository
     */
    private $clinicRepository;
    /**
     * @var EarningRepository
     */
    private $earningRepository;

    public function __construct(ClinicPayoutRepository $clinicPayoutRepo, CustomFieldRepository $customFieldRepo, ClinicRepository $clinicRepo, EarningRepository $earningRepository)
    {
        parent::__construct();
        $this->clinicPayoutRepository = $clinicPayoutRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->clinicRepository = $clinicRepo;
        $this->earningRepository = $earningRepository;
    }

    /**
     * Display a listing of the ClinicPayout.
     *
     * @param ClinicPayoutDataTable $clinicPayoutDataTable
     * @return Response
     */
    public function index(ClinicPayoutDataTable $clinicPayoutDataTable)
    {
        return $clinicPayoutDataTable->render('clinic_payouts.index');
    }

    /**
     * Show the form for creating a new ClinicPayout.
     *
     * @param int $id
     * @return Application|Factory|Response|View
     * @throws RepositoryException
     */
    public function create(int $id)
    {
        $this->clinicRepository->pushCriteria(new ClinicsOfUserCriteria(auth()->id()));
        $clinic = $this->clinicRepository->findWithoutFail($id);
        if (empty($clinic)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.clinic')]));
            return redirect(route('clinicPayouts.index'));
        }
        $earning = $this->earningRepository->findByField('clinic_id', $id)->first();
        $totalPayout = $this->clinicPayoutRepository->findByField('clinic_id', $id)->sum("amount");

        $hasCustomField = in_array($this->clinicPayoutRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->clinicPayoutRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('clinic_payouts.create')->with("customFields", isset($html) ? $html : false)->with("clinic", $clinic)->with("amount", $earning->clinic_earning - $totalPayout);
    }

    /**
     * Store a newly created ClinicPayout in storage.
     *
     * @param CreateClinicPayoutRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function store(CreateClinicPayoutRequest $request)
    {
        $input = $request->all();
        $earning = $this->earningRepository->findByField('clinic_id', $input['clinic_id'])->first();
        $totalPayout = $this->clinicPayoutRepository->findByField('clinic_id', $input['clinic_id'])->sum("amount");
        $input['amount'] = $earning->clinic_earning - $totalPayout;
        if ($input['amount'] <= 0) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.earning')]));
            return redirect(route('clinicPayouts.index'));
        }
        $input['paid_date'] = Carbon::now();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->clinicPayoutRepository->model());
        try {
            $clinicPayout = $this->clinicPayoutRepository->create($input);
            $clinicPayout->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.clinic_payout')]));

        return redirect(route('clinicPayouts.index'));
    }
}

<?php
/*
 * File name: EarningController.php
 * Last modified: 2021.02.22 at 14:40:34
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers;

use App\Criteria\Appointments\AppointmentOfClinicCriteria;
use App\Criteria\Appointments\AppointmentOfDoctorCriteria;
use App\Criteria\Appointments\PaidAppointmentsCriteria;
use App\DataTables\EarningDataTable;
use App\Http\Requests\CreateEarningRequest;
use App\Repositories\AppointmentRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\DoctorRepository;
use App\Repositories\EarningRepository;
use App\Repositories\ClinicRepository;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

class EarningController extends Controller
{
    /** @var  EarningRepository */
    private $earningRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var ClinicRepository
     */
    private $clinicRepository;

    /**
     * @var DoctorRepository
     */
    private $doctorRepository;
    /**
     * @var AppointmentRepository
     */
    private $appointmentRepository;

    public function __construct(EarningRepository $earningRepo, CustomFieldRepository $customFieldRepo, ClinicRepository $clinicRepo,DoctorRepository $doctorRepo, AppointmentRepository $appointmentRepository)
    {
        parent::__construct();
        $this->earningRepository = $earningRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->doctorRepository = $doctorRepo;
        $this->clinicRepository = $clinicRepo;
        $this->appointmentRepository = $appointmentRepository;
    }

    /**
     * Display a listing of the Earning.
     *
     * @param EarningDataTable $earningDataTable
     * @return Response
     */
    public function index(EarningDataTable $earningDataTable)
    {
        return $earningDataTable->render('earnings.index');
    }

    /**
     * Show the form for creating a new Earning.
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {
        $doctors = $this->doctorRepository->all();
        foreach ($doctors as $doctor) {
            try {
                $this->appointmentRepository->pushCriteria(new AppointmentOfDoctorCriteria($doctor->id));
                $this->appointmentRepository->pushCriteria(new PaidAppointmentsCriteria());
                $clinic = $this->clinicRepository->findWithoutFail($doctor->clinic_id);
                //dd($clinic);
                $appointments = $this->appointmentRepository->all();
                $appointmentsCount = $appointments->count();

                $appointmentsTotals = $appointments->map(function ($appointment) {
                    return $appointment->getTotal();
                })->toArray();

                $appointmentsTaxes = $appointments->map(function ($appointment) {
                    return $appointment->getTaxesValue();
                })->toArray();

                $total = array_reduce($appointmentsTotals, function ($total1, $total2) {
                    return $total1 + $total2;
                }, 0);

                $tax = array_reduce($appointmentsTaxes, function ($tax1, $tax2) {
                    return $tax1 + $tax2;
                }, 0);
                $this->earningRepository->updateOrCreate(['doctor_id' => $doctor->id], [
                        'clinic_id' => $clinic->id,
                        'total_appointments' => $appointmentsCount,
                        'total_earning' => $total - $tax,
                        'taxes' => $tax,
                        'doctor_earning' => ($total - $tax) * $doctor->commission / 100,
                        'admin_earning' => ($total - $tax) * (100 - $doctor->commission) / 100 * (100 - $clinic->clinicLevel->commission) / 100,
                        'clinic_earning' => ($total - $tax) * (100 - $doctor->commission) / 100 * ($clinic->clinicLevel->commission) / 100,
                    ]
                );
            } catch (ValidatorException | RepositoryException $e) {
            } finally {
                $this->appointmentRepository->resetCriteria();
            }
        }
        return redirect(route('earnings.index'));
    }

    /**
     * Store a newly created Earning in storage.
     *
     * @param CreateEarningRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function store(CreateEarningRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->earningRepository->model());
        try {
            $earning = $this->earningRepository->create($input);
            $earning->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.earning')]));

        return redirect(route('earnings.index'));
    }

    /**
     * Remove the specified Earning from storage.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function destroy($id)
    {
        $earning = $this->earningRepository->findWithoutFail($id);

        if (empty($earning)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.earning')]));

            return redirect(route('earnings.index'));
        }

        $this->earningRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.earning')]));
        return redirect(route('earnings.index'));
    }
}

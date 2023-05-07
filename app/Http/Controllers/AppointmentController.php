<?php
/*
 * File name: AppointmentController.php
 * Last modified: 2021.11.01 at 22:25:44
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers;

use App\Criteria\Addresses\AddressesOfUserCriteria;
use App\Criteria\Appointments\AppointmentsOfPatientCriteria;
use App\DataTables\AppointmentDataTable;
use App\Events\AppointmentChangedEvent;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Patient;
use App\Notifications\StatusChangedAppointment;
use App\Repositories\AddressRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\AppointmentStatusRepository;
use App\Repositories\CouponRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\DoctorRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\OptionRepository;
use App\Repositories\PatientRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PaymentStatusRepository;
use App\Repositories\TaxRepository;
use App\Repositories\UserRepository;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

class AppointmentController extends Controller
{
    /** @var  AppointmentRepository */
    private $appointmentRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var AppointmentStatusRepository
     */
    private $appointmentStatusRepository;
    /**
     * @var PaymentRepository
     */
    private $paymentRepository;
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;
    /**
     * @var AddressRepository
     */
    private $addressRepository;
    /**
     * @var TaxRepository
     */
    private $taxRepository;
    /**
     * @var DoctorRepository
     */
    private $doctorRepository;
    /**
     * @var ClinicRepository
     */
    private $clinicRepository;
    /**
     * @var CouponRepository
     */
    private $couponRepository;
    /**
     * @var PatientRepository
     */
    private $patientRepository;
    /**
     * @var PaymentStatusRepository
     */
    private $paymentStatusRepository;

    public function __construct(AppointmentRepository $appointmentRepo, CustomFieldRepository $customFieldRepo, UserRepository $userRepo
        , AppointmentStatusRepository                 $appointmentStatusRepo, NotificationRepository $notificationRepo, PaymentRepository $paymentRepo, AddressRepository $addressRepository, TaxRepository $taxRepository, DoctorRepository $doctorRepository, ClinicRepository $clinicRepository, CouponRepository $couponRepository, PatientRepository $patientRepository, PaymentStatusRepository $paymentStatusRepository)
    {
        parent::__construct();
        $this->appointmentRepository = $appointmentRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->userRepository = $userRepo;
        $this->appointmentStatusRepository = $appointmentStatusRepo;
        $this->notificationRepository = $notificationRepo;
        $this->paymentRepository = $paymentRepo;
        $this->addressRepository = $addressRepository;
        $this->taxRepository = $taxRepository;
        $this->doctorRepository = $doctorRepository;
        $this->clinicRepository = $clinicRepository;
        $this->couponRepository = $couponRepository;
        $this->patientRepository = $patientRepository;
        $this->paymentStatusRepository = $paymentStatusRepository;
    }

    /**
     * Display a listing of the Appointment.
     *
     * @param AppointmentDataTable $appointmentDataTable
     * @return Response
     */
    public function index(AppointmentDataTable $appointmentDataTable)
    {
        return $appointmentDataTable->render('appointments.index');
    }

    /**
     * Display the specified Appointment.
     *
     * @param int $id
     *
     * @return Application|Factory|Response|View
     * @throws RepositoryException
     */
    public function show(int $id)
    {
        $this->appointmentRepository->pushCriteria(new AppointmentsOfPatientCriteria(auth()->id()));
        $appointment = $this->appointmentRepository->findWithoutFail($id);
        if (empty($appointment)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.appointment')]));
            return redirect(route('appointments.index'));
        }
        $appointmentStatuses = $this->appointmentStatusRepository->orderBy('order')->all();
        return view('appointments.show')->with('appointment', $appointment)->with('appointmentStatuses', $appointmentStatuses);
    }

    /**
     * Show the form for editing the specified Appointment.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function edit(int $id)
    {
        $this->appointmentRepository->pushCriteria(new AppointmentsOfPatientCriteria(auth()->id()));
        $appointment = $this->appointmentRepository->findWithoutFail($id);
        if (empty($appointment)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.appointment')]));
            return redirect(route('appointments.index'));
        }
        array_push($appointment->fillable, ['address_id', 'payment_status_id']);
        $appointment->address_id = $appointment->address->id;
        $appointmentStatus = $this->appointmentStatusRepository->orderBy('order')->pluck('status', 'id');
        if (!empty($appointment->payment_id)) {
            $appointment->payment_status_id = $appointment->payment->payment_status_id;
            $paymentStatuses = $this->paymentStatusRepository->pluck('status', 'id');
        } else {
            $paymentStatuses = null;
        }
        $addresses = $this->addressRepository->getByCriteria(new AddressesOfUserCriteria($appointment->user_id))->pluck('address', 'id');

        $customFieldsValues = $appointment->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->appointmentRepository->model());
        $hasCustomField = in_array($this->appointmentRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
        return view('appointments.edit')->with('appointment', $appointment)->with("customFields", isset($html) ? $html : false)->with("appointmentStatus", $appointmentStatus)->with("addresses", $addresses)->with("paymentStatuses", $paymentStatuses);
    }

    /**
     * Update the specified Appointment in storage.
     *
     * @param int $id
     * @param UpdateAppointmentRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function update(int $id, UpdateAppointmentRequest $request)
    {
        $oldAppointment = $this->appointmentRepository->findWithoutFail($id);
        if (empty($oldAppointment)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.appointment')]));
            return redirect(route('appointments.index'));
        }
        $input = $request->all();
        $address = $this->addressRepository->findWithoutFail($input['address_id']);
        $input['address'] = $address;
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->appointmentRepository->model());
        try {
            $appointment = $this->appointmentRepository->update($input, $id);
            $user = $appointment->user;
            $doctor = $this->doctorRepository->findWithoutFail($appointment->doctor->id);
            if (isset($input['payment_status_id'])) {
                $this->paymentRepository->update(
                    ['payment_status_id' => $input['payment_status_id']],
                    $appointment->payment_id
                );
                event(new AppointmentChangedEvent($appointment));
            }

            if (isset($input['appointment_status_id']) && $input['appointment_status_id'] != $oldAppointment->appointment_status_id) {
                if ($appointment->appointmentStatus->order < 40) {
                    Notification::send([$user], new StatusChangedAppointment($appointment));
                } else {
                    Notification::send([$doctor->user], new StatusChangedAppointment($appointment));
                }
            }

            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $appointment->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }
        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.appointment')]));
        return redirect(route('appointments.index'));
    }

    /**
     * Remove the specified Appointment from storage.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function destroy($id)
    {
        if (!config('installer.demo_app')) {
            $this->appointmentRepository->pushCriteria(new AppointmentsOfPatientCriteria(auth()->id()));
            $appointment = $this->appointmentRepository->findWithoutFail($id);

            if (empty($appointment)) {
                Flash::error(__('lang.not_found', ['operator' => __('lang.appointment')]));

                return redirect(route('appointments.index'));
            }

            $this->appointmentRepository->delete($id);

            Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.appointment')]));

        } else {
            Flash::warning('This is only demo app you can\'t change this section ');
        }
        return redirect(route('appointments.index'));
    }

}

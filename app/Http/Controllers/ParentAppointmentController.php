<?php
/*
 * File name: ParentAppointmentController.php
 * Last modified: 2021.06.09 at 17:20:26
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers;

use App\Events\AppointmentChangedEvent;
use App\Models\Appointment;
use App\Notifications\NewAppointment;
use App\Repositories\AppointmentRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Prettus\Validator\Exceptions\ValidatorException;

abstract class ParentAppointmentController extends Controller
{
    /** @var  AppointmentRepository */
    protected $appointmentRepository;
    /** @var  PaymentRepository */
    protected $paymentRepository;
    /** @var  NotificationRepository */
    protected $notificationRepository;
    /** @var Appointment */
    protected $appointment;
    /** @var int */
    protected $paymentMethodId;

    /**
     * AppointmentAPIController constructor.
     * @param AppointmentRepository $appointmentRepo
     * @param PaymentRepository $paymentRepo
     * @param NotificationRepository $notificationRepo
     */
    public function __construct(AppointmentRepository $appointmentRepo, PaymentRepository $paymentRepo, NotificationRepository $notificationRepo)
    {
        parent::__construct();
        $this->appointmentRepository = $appointmentRepo;
        $this->paymentRepository = $paymentRepo;
        $this->notificationRepository = $notificationRepo;
        $this->appointment = new Appointment();

        $this->__init();
    }

    abstract public function __init();

    protected function createAppointment()
    {
        try {
            $payment = $this->createPayment();
            if ($payment != null) {
                $this->appointmentRepository->update(['payment_id' => $payment->id], $this->appointment->id);
                event(new AppointmentChangedEvent($this->appointment->doctor));
                $this->sendNotificationToDoctors();
                $this->sendNotificationToClinicsOwners();
            }
        } catch (ValidatorException $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @return mixed
     * @throws ValidatorException
     */
    protected function createPayment()
    {
        if ($this->appointment != null && $this->paymentMethodId != null) {
            $input['amount'] = $this->appointment->getTotal();
            $input['description'] = trans("lang.done");
            $input['payment_status_id'] = 2;
            $input['payment_method_id'] = $this->paymentMethodId;
            $input['user_id'] = $this->appointment->user_id;
            try {
                return $this->paymentRepository->create($input);
            } catch (ValidatorException $e) {
                Log::error($e->getMessage());
            }
        }
        return null;
    }

    protected function sendNotificationToDoctors()
    {
        Notification::send($this->appointment->doctor->user, new NewAppointment($this->appointment));
    }
    protected function sendNotificationToClinicsOwners()
    {
        Notification::send($this->appointment->clinic->users, new NewAppointment($this->appointment));
    }

}

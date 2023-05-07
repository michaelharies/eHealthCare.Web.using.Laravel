<?php
/*
 * File name: UpdateAppointmentEarningTable.php
 * Last modified: 2021.06.10 at 20:37:20
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Listeners;

use App\Criteria\Appointments\AppointmentOfClinicCriteria;
use App\Criteria\Appointments\AppointmentOfDoctorCriteria;
use App\Criteria\Appointments\PaidAppointmentsCriteria;
use App\Repositories\AppointmentRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\EarningRepository;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class UpdateAppointmentEarningTable
 * @package App\Listeners
 */
class UpdateAppointmentEarningTable
{
    /**
     * @var EarningRepository
     */
    private $earningRepository;

    /**
     * @var AppointmentRepository
     */
    private $appointmentRepository;

    /**
     * @var ClinicRepository
     */
    private $clinicRepository;

    /**
     * Create the event listener.
     *
     * @param EarningRepository $earningRepository
     * @param AppointmentRepository $appointmentRepository
     * @param ClinicRepository $clinicRepository
     */
    public function __construct(EarningRepository $earningRepository, AppointmentRepository $appointmentRepository, ClinicRepository $clinicRepository)
    {
        $this->earningRepository = $earningRepository;
        $this->appointmentRepository = $appointmentRepository;
        $this->clinicRepository = $clinicRepository;
    }

    /**
     * Handle the event.
     * oldAppointment
     * updatedAppointment
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        try {
            $this->appointmentRepository->pushCriteria(new AppointmentOfDoctorCriteria($event->appointment->doctor->id));
            $this->appointmentRepository->pushCriteria(new PaidAppointmentsCriteria());
            $appointments = $this->appointmentRepository->all();
            $appointmentsCount = $appointments->count();
            $clinic = $event->appointment->clinic;
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
            $this->earningRepository->updateOrCreate(['doctor_id' => $event->appointment->doctor->id], [
                    'clinic_id' => $clinic->id,
                    'total_appointments' => $appointmentsCount,
                    'total_earning' => $total - $tax,
                    'taxes' => $tax,
                    'doctor_earning' => ($total - $tax) * $event->appointment->doctor->commission / 100,
                    'admin_earning' => ($total - $tax) * (100 - $event->appointment->doctor->commission) / 100 * (100 - $clinic->clinicLevel->commission) / 100,
                    'clinic_earning' => ($total - $tax) * (100 - $event->appointment->doctor->commission) / 100 * ($clinic->clinicLevel->commission) / 100,
                ]
            );
        } catch (ValidatorException | RepositoryException $e) {
        } finally {
            $this->appointmentRepository->resetCriteria();
        }
    }
}

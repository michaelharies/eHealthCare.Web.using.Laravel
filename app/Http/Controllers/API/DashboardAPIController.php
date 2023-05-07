<?php
/*
 * File name: DashboardAPIController.php
 * Last modified: 2021.02.21 at 14:56:24
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers\API;

use App\Criteria\Appointments\AppointmentsOfPatientCriteria;
use App\Criteria\Earnings\EarningOfUserCriteria;
use App\Criteria\Clinics\ClinicsOfUserCriteria;
use App\Criteria\Doctors\DoctorsOfUserCriteria;
use App\Http\Controllers\Controller;
use App\Repositories\AppointmentRepository;
use App\Repositories\EarningRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\DoctorRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Prettus\Repository\Exceptions\RepositoryException;

class DashboardAPIController extends Controller
{
    /** @var  AppointmentRepository */
    private $appointmentRepository;

    /** @var  ClinicRepository */
    private $clinicRepository;
    /**
     * @var DoctorRepository
     */
    private $doctorRepository;
    /**
     * @var EarningRepository
     */
    private $earningRepository;

    public function __construct(AppointmentRepository $appointmentRepo, EarningRepository $earningRepo, ClinicRepository $clinicRepo, DoctorRepository $doctorRepo)
    {
        parent::__construct();
        $this->appointmentRepository = $appointmentRepo;
        $this->clinicRepository = $clinicRepo;
        $this->doctorRepository = $doctorRepo;
        $this->earningRepository = $earningRepo;
    }

    /**
     * Display a listing of the Faq.
     * GET|HEAD /clinic/dashboard
     * @param Request $request
     * @return JsonResponse
     */
    public function clinic(Request $request): JsonResponse
    {
        $statistics = [];
        try {

            $this->earningRepository->pushCriteria(new EarningOfUserCriteria(auth()->id()));
            $earning['description'] = 'total_earning';
            $earning['value'] = $this->earningRepository->all()->sum('clinic_earning');
            $statistics[] = $earning;

            $this->appointmentRepository->pushCriteria(new AppointmentsOfPatientCriteria(auth()->id()));
            $appointmentsCount['description'] = "total_appointments";
            $appointmentsCount['value'] = $this->appointmentRepository->all('appointments.id')->count();
            $statistics[] = $appointmentsCount;

            $this->clinicRepository->pushCriteria(new ClinicsOfUserCriteria(auth()->id()));
            $clinicsCount['description'] = "total_clinics";
            $clinicsCount['value'] = $this->clinicRepository->all('clinics.id')->count();
            $statistics[] = $clinicsCount;

            $this->doctorRepository->pushCriteria(new DoctorsOfUserCriteria(auth()->id()));
            $doctorsCount['description'] = "total_doctors";
            $doctorsCount['value'] = $this->doctorRepository->all('doctors.id')->count();
            $statistics[] = $doctorsCount;


        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($statistics, 'Statistics retrieved successfully');
    }

    /**
     * Display a listing of the Faq.
     * GET|HEAD /clinic/dashboard
     * @param Request $request
     * @return JsonResponse
     */
    public function doctor(Request $request): JsonResponse
    {
        $statistics = [];
        try {

            $this->earningRepository->pushCriteria(new EarningOfUserCriteria(auth()->id()));
            $earning['description'] = 'doctor_earning';
            $earning['value'] = $this->earningRepository->all()->sum('doctor_earning');
            $statistics[] = $earning;

            $this->appointmentRepository->pushCriteria(new AppointmentsOfPatientCriteria(auth()->id()));
            $appointmentsCount['description'] = "total_appointments";
            $appointmentsCount['value'] = $this->appointmentRepository->all('appointments.id')->count();
            $statistics[] = $appointmentsCount;

            $this->doctorRepository->pushCriteria(new DoctorsOfUserCriteria(auth()->id()));
            $doctorsCount['description'] = "total_doctors";
            $doctorsCount['value'] = $this->doctorRepository->all('doctors.id')->count();
            $statistics[] = $doctorsCount;

            $this->clinicRepository->pushCriteria(new ClinicsOfUserCriteria(auth()->id()));
            $clinicsCount['description'] = "total_clinics";
            $clinicsCount['value'] = $this->clinicRepository->all('clinics.id')->count();
            $statistics[] = $clinicsCount;

        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($statistics, 'Statistics retrieved successfully');
    }
}

<?php
/*
 * File name: AppointmentRepository.php
 * Last modified: 2021.01.28 at 23:46:24
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Repositories;

use App\Models\Appointment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AppointmentRepository
 * @package App\Repositories
 * @version January 25, 2021, 9:22 pm UTC
 *
 * @method Appointment findWithoutFail($id, $columns = ['*'])
 * @method Appointment find($id, $columns = ['*'])
 * @method Appointment first($columns = ['*'])
 */
class AppointmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'clinic',
        'doctor',
        'patient',
        'user_id',
        'appointment_status_id',
        'address',
        'payment_id',
        'coupon',
        'taxes',
        'appointment_at',
        'start_at',
        'ends_at',
        'hint'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Appointment::class;
    }
}

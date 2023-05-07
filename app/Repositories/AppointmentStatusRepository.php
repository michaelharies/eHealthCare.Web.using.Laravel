<?php
/*
 * File name: AppointmentStatusRepository.php
 * Last modified: 2021.01.25 at 22:00:21
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Repositories;

use App\Models\AppointmentStatus;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AppointmentStatusRepository
 * @package App\Repositories
 * @version January 25, 2021, 7:18 pm UTC
 *
 * @method AppointmentStatus findWithoutFail($id, $columns = ['*'])
 * @method AppointmentStatus find($id, $columns = ['*'])
 * @method AppointmentStatus first($columns = ['*'])
 */
class AppointmentStatusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'status',
        'order'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AppointmentStatus::class;
    }
}

<?php
/*
 * File name: ClinicPayoutRepository.php
 * Last modified: 2021.01.30 at 16:06:30
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Repositories;

use App\Models\ClinicPayout;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ClinicPayoutRepository
 * @package App\Repositories
 * @version January 30, 2021, 11:17 am UTC
 *
 * @method ClinicPayout findWithoutFail($id, $columns = ['*'])
 * @method ClinicPayout find($id, $columns = ['*'])
 * @method ClinicPayout first($columns = ['*'])
 */
class ClinicPayoutRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'clinic_id',
        'method',
        'amount',
        'paid_date',
        'note'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ClinicPayout::class;
    }
}

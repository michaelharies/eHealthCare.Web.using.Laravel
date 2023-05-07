<?php
/*
 * File name: ClinicLevelRepository.php
 * Last modified: 2022.02.03 at 14:23:26
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Repositories;

use App\Models\ClinicLevel;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ClinicLevelRepository
 * @package App\Repositories
 * @version January 13, 2021, 10:56 am UTC
 *
 * @method ClinicLevel findWithoutFail($id, $columns = ['*'])
 * @method ClinicLevel find($id, $columns = ['*'])
 * @method ClinicLevel first($columns = ['*'])
 */
class ClinicLevelRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'commission',
        'disabled',
        'default'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ClinicLevel::class;
    }
}

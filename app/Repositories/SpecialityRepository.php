<?php
/*
 * File name: SpecialityRepository.php
 * Last modified: 2021.01.31 at 14:03:57
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Repositories;

use App\Models\Speciality;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SpecialityRepository
 * @package App\Repositories
 * @version January 19, 2021, 2:04 pm UTC
 *
 * @method Speciality findWithoutFail($id, $columns = ['*'])
 * @method Speciality find($id, $columns = ['*'])
 * @method Speciality first($columns = ['*'])
 */
class SpecialityRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'color',
        'description',
        'featured',
        'order',
        'parent_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Speciality::class;
    }
}

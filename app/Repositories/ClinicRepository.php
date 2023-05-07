<?php
/*
 * File name: ClinicRepository.php
 * Last modified: 2021.01.17 at 17:04:35
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Repositories;

use App\Models\Clinic;
use InfyOm\Generator\Common\BaseRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Class ClinicRepository
 * @package App\Repositories
 * @version January 13, 2021, 11:11 am UTC
 *
 * @method Clinic findWithoutFail($id, $columns = ['*'])
 * @method Clinic find($id, $columns = ['*'])
 * @method Clinic first($columns = ['*'])
 */
class ClinicRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;


    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
        'phone_number',
        'mobile_number',
        'availability_range',
        'available',
        'featured'
    ];

    /**
     * Configure the Model
     **/
    public function model(): string
    {
        return Clinic::class;
    }
}

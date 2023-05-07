<?php
/*
 * File name: DoctorRepository.php
 * Last modified: 2021.03.25 at 18:04:58
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Repositories;

use App\Models\Doctor;
use InfyOm\Generator\Common\BaseRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Class DoctorRepository
 * @package App\Repositories
 * @version January 19, 2021, 1:59 pm UTC
 *
 * @method Doctor findWithoutFail($id, $columns = ['*'])
 * @method Doctor find($id, $columns = ['*'])
 * @method Doctor first($columns = ['*'])
 */
class DoctorRepository extends BaseRepository implements CacheableInterface
{

    use CacheableRepository;

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'price',
        'discount_price',
        'description',
        'featured',
        'available',
        'enable_appointment',
        'enable_at_clinic',
        'enable_at_customer_address',
        'clinic_id',
        'user_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Doctor::class;
    }

    /**
     * @return array
     */
    public function groupedByClinics(): array
    {
        $doctors = [];
        foreach ($this->all() as $model) {
            if (!empty($model->clinic)) {
                $doctors[$model->clinic->name][$model->id] = $model->name;
            }
        }
        return $doctors;
    }
}

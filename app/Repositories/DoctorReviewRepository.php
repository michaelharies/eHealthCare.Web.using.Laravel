<?php
/*
 * File name: DoctorReviewRepository.php
 * Last modified: 2021.01.23 at 21:01:19
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Repositories;

use App\Models\DoctorReview;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DoctorReviewRepository
 * @package App\Repositories
 * @version January 23, 2021, 7:42 pm UTC
 *
 * @method DoctorReview findWithoutFail($id, $columns = ['*'])
 * @method DoctorReview find($id, $columns = ['*'])
 * @method DoctorReview first($columns = ['*'])
 */
class DoctorReviewRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'review',
        'rate',
        'user_id',
        'doctor_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DoctorReview::class;
    }
}

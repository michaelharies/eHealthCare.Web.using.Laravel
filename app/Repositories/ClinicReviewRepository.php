<?php

namespace App\Repositories;

use App\Models\ClinicReview;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ClinicReviewRepository
 * @package App\Repositories
 * @version October 23, 2022, 5:11 pm AST
 *
 * @method ClinicReview findWithoutFail($id, $columns = ['*'])
 * @method ClinicReview find($id, $columns = ['*'])
 * @method ClinicReview first($columns = ['*'])
*/
class ClinicReviewRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'review',
        'rate',
        'user_id',
        'clinic_id'
    ];

    /**
     * Configure the Model
     **/
    public function model(): string
    {
        return ClinicReview::class;
    }
}

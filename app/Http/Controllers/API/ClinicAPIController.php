<?php
/*
 * File name: DoctorAPIController.php
 * Last modified: 2022.04.13 at 12:23:04
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\API;


use App\Criteria\Clinics\AcceptedCriteria;
use App\Criteria\Clinics\ClinicsOfUserCriteria;
use App\Criteria\Clinics\NearCriteria;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateClinicRequest;
use App\Http\Requests\UpdateClinicRequest;
use App\Repositories\ClinicRepository;
use App\Repositories\UploadRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class ClinicController
 * @package App\Http\Controllers\API
 */
class ClinicAPIController extends Controller
{
    /** @var  ClinicRepository */
    private $clinicRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(ClinicRepository $clinicRepo, UploadRepository $uploadRepo)
    {
        $this->clinicRepository = $clinicRepo;
        $this->uploadRepository = $uploadRepo;
        parent::__construct();
    }


    /**
     * Display a listing of the Clinic.
     * GET|HEAD /clinics
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->clinicRepository->pushCriteria(new RequestCriteria($request));
            $this->clinicRepository->pushCriteria(new AcceptedCriteria());
            $this->clinicRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->clinicRepository->pushCriteria(new NearCriteria($request));

        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }

        $clinics = $this->clinicRepository->all();
        $this->filterCollection($request, $clinics);

        return $this->sendResponse($clinics->toArray(), 'Clinics retrieved successfully');
    }

    /**
     * Display the specified Clinic.
     * GET|HEAD /clinics/{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $this->clinicRepository->pushCriteria(new RequestCriteria($request));
            $this->clinicRepository->pushCriteria(new AcceptedCriteria());
            $this->clinicRepository->pushCriteria(new LimitOffsetCriteria($request));

        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        $clinic = $this->clinicRepository->findWithoutFail($id);
        if (empty($clinic)) {
            return $this->sendError('Clinic not found');
        }
        $this->filterModel($request, $clinic);
        return $this->sendResponse($clinic->toArray(), 'Clinic retrieved successfully');
    }


    /**
     * Store a newly created Doctor in storage.
     *
     * @param CreateClinicRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateClinicRequest $request): JsonResponse
    {
        try {
            $input = $request->all();
            if (auth()->user()->hasAnyRole(['clinic_owner', 'customer'])) {
                $input['users'] = [auth()->id()];
                $input['accepted'] = 0;
                $input['featured'] = 0;
                $input['available'] = 1;
            }
            $clinic = $this->clinicRepository->create($input);
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                foreach ($input['image'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($clinic, 'image');
                }
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($clinic->toArray(), __('lang.saved_successfully', ['operator' => __('lang.clinic')]));
    }

    /**
     * Update the specified Doctor in storage.
     *
     * @param int $id
     * @param UpdateClinicRequest $request
     *
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function update(int $id, UpdateClinicRequest $request): JsonResponse
    {
        $this->clinicRepository->pushCriteria(new ClinicsOfUserCriteria(auth()->id()));
        $clinic = $this->clinicRepository->findWithoutFail($id);

        if (empty($clinic)) {
            return $this->sendError('Clinic not found');
        }
        try {
            $input = $request->all();
            $clinic = $this->clinicRepository->update($input, $id);
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
//                if ($clinic->hasMedia('image')) {
//                    $clinic->getMedia('image')->each->delete();
//                }
                foreach ($input['image'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($clinic, 'image');
                }
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($clinic->toArray(), __('lang.updated_successfully', ['operator' => __('lang.clinic')]));
    }

    /**
     * Remove the specified Doctor from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->clinicRepository->pushCriteria(new ClinicsOfUserCriteria(auth()->id()));
        $clinic = $this->clinicRepository->findWithoutFail($id);
        if (empty($clinic)) {
            return $this->sendError('Clinic not found');
        }
        $this->clinicRepository->delete($id);
        return $this->sendResponse($clinic, __('lang.deleted_successfully', ['operator' => __('lang.clinic')]));

    }
}

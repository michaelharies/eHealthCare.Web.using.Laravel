<?php
/*
 * File name: DoctorAPIController.php
 * Last modified: 2022.04.13 at 08:14:30
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\API;


use App\Criteria\Doctors\DoctorsOfUserCriteria;
use App\Criteria\Doctors\NearCriteria;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Repositories\DoctorRepository;
use App\Repositories\UploadRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Nwidart\Modules\Facades\Module;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class DoctorController
 * @package App\Http\Controllers\API
 */
class DoctorAPIController extends Controller
{
    /** @var  DoctorRepository */
    private $doctorRepository;
    /** @var UserRepository */
    private $userRepository;
    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(DoctorRepository $doctorRepo, UserRepository $userRepository, UploadRepository $uploadRepository)
    {
        parent::__construct();
        $this->doctorRepository = $doctorRepo;
        $this->userRepository = $userRepository;
        $this->uploadRepository = $uploadRepository;
    }

    /**
     * Display a listing of the Doctor.
     * GET|HEAD /doctors
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->doctorRepository->pushCriteria(new RequestCriteria($request));
            $this->doctorRepository->pushCriteria(new DoctorsOfUserCriteria(auth()->id()));
            $this->doctorRepository->pushCriteria(new NearCriteria($request));

        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }

        $doctors = $this->doctorRepository->all();


        // if (!$request->has('all')) {
        //     $this->availableDoctors($doctors);
        // }
        $this->hasValidSubscription($request, $doctors);
        $this->orderByRating($request, $doctors);
        $this->limitOffset($request, $doctors);
        $this->filterCollection($request, $doctors);
        $doctors = array_values($doctors->toArray());

        return $this->sendResponse($doctors, 'Doctors retrieved successfully');
    }

    /**
     * @param Collection $doctors
     */
    private function availableDoctors(Collection &$doctors)
    {
        $doctors = $doctors->where('available', true);
    }

    /**
     * @param Request $request
     * @param Collection $doctors
     */
    private function hasValidSubscription(Request $request, Collection &$doctors)
    {
        if (Module::isActivated('Subscription')) {
            $doctors = $doctors->filter(function ($element) {
                return $element->clinic->hasValidSubscription && $element->clinic->accepted;
            });
        } else {
            $doctors = $doctors->filter(function ($element) {
                return $element->clinic->accepted;
            });
        }
    }

    /**
     * @param Request $request
     * @param Collection $doctors
     */
    private function orderByRating(Request $request, Collection &$doctors)
    {
        if ($request->has('rating')) {
            $doctors = $doctors->sortBy('rate', SORT_REGULAR, true);
        }
    }

    /**
     * Display the specified Doctor.
     * GET|HEAD /doctors/{id}
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $this->doctorRepository->pushCriteria(new RequestCriteria($request));
            $this->doctorRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $doctor = $this->doctorRepository->findWithoutFail($id);
        if (empty($doctor)) {
            return $this->sendError('Doctor not found');
        }
        if ($request->has('api_token')) {
            $user = $this->userRepository->findByField('api_token', $request->input('api_token'))->first();
            if (!empty($user)) {
                auth()->login($user, true);
            }
        }
        $this->filterModel($request, $doctor);

        return $this->sendResponse($doctor->toArray(), 'Doctor retrieved successfully');
    }

    /**
     * Store a newly created Doctor in storage.
     *
     * @param CreateDoctorRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateDoctorRequest $request): JsonResponse
    {
        try {
            $input = $request->all();
            $doctor = $this->doctorRepository->create($input);
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                foreach ($input['image'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($doctor, 'image');
                }
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($doctor->toArray(), __('lang.saved_successfully', ['operator' => __('lang.doctor')]));
    }

    /**
     * Update the specified Doctor in storage.
     *
     * @param int $id
     * @param UpdateDoctorRequest $request
     *
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function update(int $id, UpdateDoctorRequest $request): JsonResponse
    {
        $this->doctorRepository->pushCriteria(new DoctorsOfUserCriteria(auth()->id()));
        $doctor = $this->doctorRepository->findWithoutFail($id);

        if (empty($doctor)) {
            return $this->sendError('Doctor not found');
        }
        try {
            $input = $request->all();
            $input['specialities'] = isset($input['specialities']) ? $input['specialities'] : [];
            $doctor = $this->doctorRepository->update($input, $id);
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
//                if ($doctor->hasMedia('image')) {
//                    $doctor->getMedia('image')->each->delete();
//                }
                foreach ($input['image'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($doctor, 'image');
                }
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($doctor->toArray(), __('lang.updated_successfully', ['operator' => __('lang.doctor')]));
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
        $this->doctorRepository->pushCriteria(new DoctorsOfUserCriteria(auth()->id()));
        $doctor = $this->doctorRepository->findWithoutFail($id);

        if (empty($doctor)) {
            return $this->sendError('Doctor not found');
        }

        $doctor = $this->doctorRepository->delete($id);

        return $this->sendResponse($doctor, __('lang.deleted_successfully', ['operator' => __('lang.doctor')]));

    }

    /**
     * Remove Media of Doctor
     * @param Request $request
     * @throws RepositoryException
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        try {
            $this->doctorRepository->pushCriteria(new DoctorsOfUserCriteria(auth()->id()));
            $doctor = $this->doctorRepository->findWithoutFail($input['id']);
            if ($doctor->hasMedia($input['collection'])) {
                $doctor->getFirstMedia($input['collection'])->delete();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}

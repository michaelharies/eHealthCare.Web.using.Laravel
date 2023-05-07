<?php
/*
 * File name: ModuleAPIController.php
 * Last modified: 2022.04.01 at 23:11:05
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Nwidart\Modules\Facades\Module;

/**
 * Class ModuleAPIController
 * @package App\Http\Controllers\API
 */
class ModuleAPIController extends Controller
{

    /**
     * Display a listing of the Module.
     * GET|HEAD /modules
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $array = Module::allEnabled();
        $array = array_keys($array);
        return $this->sendResponse($array, 'Modules retrieved successfully');
    }

}

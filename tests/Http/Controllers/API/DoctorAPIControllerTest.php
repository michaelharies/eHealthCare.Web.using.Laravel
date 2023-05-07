<?php
/*
 * File name: DoctorAPIControllerTest.php
 * Last modified: 2021.02.05 at 21:04:54
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace Tests\Http\Controllers\API;

use Illuminate\Http\Response;
use Tests\Helpers\TestHelper;
use Tests\TestCase;

class DoctorAPIControllerTest extends TestCase
{

    public function testShow()
    {

        $response = $this->json('get', 'api/doctors/17');
        $response->assertStatus(200);
    }

    public function testGetDoctorsBySpeciality()
    {
        $queryParameters = [
            'with' => 'clinic;clinic.addresses;specialities',
            'search' => 'specialities.id:4',
            'searchFields' => 'specialities.id:=',
        ];

        $response = $this->json('get', 'api/doctors', $queryParameters);
        $data = TestHelper::generateJsonArray(count($response->json('data')), [
            'available' => true,
            'clinic' => [
                'accepted' => true,
            ]
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['data' => $data]);
    }

    public function testGetRecommendedDoctors()
    {
        $queryParameters = [
            'only' => 'id;name;price;discount_price;price_unit;has_media;media;total_reviews;rate;available',
            'limit' => '6',
        ];

        $response = $this->json('get', 'api/doctors', $queryParameters);
        $data = TestHelper::generateJsonArray(count($response->json('data')), [
            'available' => true,
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['data' => $data]);
    }

    public function testGetFeaturedDoctorsBySpeciality()
    {
        $queryParameters = [
            'with' => 'clinic;clinic.addresses;specialities',
            'search' => 'specialities.id:4;featured:1',
            'searchFields' => 'specialities.id:=;featured:=',
            'searchJoin' => 'and',
        ];

        $response = $this->json('get', 'api/doctors', $queryParameters);
        $data = TestHelper::generateJsonArray(count($response->json('data')), [
            'available' => true,
            'featured' => true,
            'clinic' => [
                'accepted' => true,
            ]
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['data' => $data]);
    }

    public function testGetAvailableDoctorsBySpeciality()
    {
        $queryParameters = [
            'with' => 'clinic;clinic.addresses;specialities',
            'search' => 'specialities.id:3',
            'searchFields' => 'specialities.id:=',
            'available_clinic' => 'true'
        ];

        $response = $this->json('get', 'api/doctors', $queryParameters);
        $response->dump();
        $data = TestHelper::generateJsonArray(count($response->json('data')), [
            'available' => true,
            'clinic' => [
                'available' => true,
                'accepted' => true,
            ]
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['data' => $data]);
    }

    public function testDestroy()
    {

    }
}

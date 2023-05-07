<?php
/*
 * File name: PayStackController.php
 * Last modified: 2021.07.23 at 21:09:46
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers;

use Flash;
use Illuminate\Http\Request;

class PayStackController extends ParentAppointmentController
{

    public function __init()
    {

    }

    public function index()
    {
        return view('home');
    }

    public function checkout(Request $request)
    {
        $this->appointment = $this->appointmentRepository->findWithoutFail($request->get('appointment_id'));
        if (empty($this->appointment)) {
            Flash::error("Error processing PayStack payment for your appointment");
            return redirect(route('payments.failed'));
        }
        return view('payment_methods.paystack_charge', ['appointment' => $this->appointment]);
    }

    public function paySuccess(Request $request, int $appointmentId, string $reference)
    {
        $this->appointment = $this->appointmentRepository->findWithoutFail($appointmentId);

        if (empty($this->appointment)) {
            Flash::error("Error processing PayStack payment for your appointment");
            return redirect(route('payments.failed'));
        } else {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.paystack.co/transaction/verify/$reference",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer " . setting('paystack_secret'),
                    "Cache-Control: no-cache",
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                return $this->sendError($err);
            } else {
                $this->paymentMethodId = 8; // Paystack method id
                $this->createAppointment();
                return $this->sendResponse($response, __('lang.saved_successfully'));
            }
        }
    }
}

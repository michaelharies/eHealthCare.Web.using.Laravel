<?php


/*
 * File name: RazorPayController.php
 * Last modified: 2021.07.31 at 15:09:35
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers;

use Exception;
use Flash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Razorpay\Api\Api;

class RazorPayController extends ParentAppointmentController
{

    /**
     * @var Api
     */
    private $api;
    private $currency;

    public function __init()
    {
        $this->api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
        $this->currency = config('installer.demo_app') ? 'INR' : setting('default_currency_code', 'INR');
    }


    public function index()
    {
        return view('home');
    }


    public function checkout(Request $request)
    {
        $this->appointment = $this->appointmentRepository->findWithoutFail($request->get('appointment_id'));
        if (!empty($this->appointment)) {
            try {
                $razorPayCart = $this->getAppointmentData();

                $razorPayAppointment = $this->api->order->create($razorPayCart);
                $fields = $this->getRazorPayFields($razorPayAppointment);
                //url-ify the data for the POST
                $fields_string = http_build_query($fields);

                //open connection
                $ch = curl_init();

                //set the url, number of POST vars, POST data
                curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/checkout/embedded');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                $result = curl_exec($ch);
                if ($result === true) {
                    die();
                }
            } catch (Exception $e) {
                Flash::error($e->getMessage());
                return redirect(route('payments.failed'));
            }
        } else {
            Flash::error("Error processing RazorPay payment for your appointment");
            return redirect(route('payments.failed'));
        }
        return null;
    }

    /**
     * Set cart data for processing payment on PayPal.
     *
     *
     * @throws Exception
     */
    private function getAppointmentData(): array
    {
        $data = [];
        $amountINR = $this->appointment->getTotal();
        $appointment_id = $this->paymentRepository->all()->count() + 1;
        $data['amount'] = (int)($amountINR * 100);
        $data['payment_capture'] = 1;
        $data['currency'] = $this->currency;
        $data['receipt'] = $appointment_id . '_' . date("Y_m_d_h_i_sa");

        return $data;
    }

    /**
     * @param $razorPayAppointment
     * @return array
     */
    private function getRazorPayFields($razorPayAppointment): array
    {

        $fields = array(
            'key_id' => config('services.razorpay.key', ''),
            'order_id' => $razorPayAppointment['id'],
            'name' => $this->appointment->clinic->name,
            'description' => $this->appointment->doctor->name,
            'image' => $this->appointment->doctor->getFirstMediaUrl('image', 'thumb'),
            'prefill' => [
                'name' => $this->appointment->user->name,
                'email' => $this->appointment->user->email,
                'contact' => config('installer.demo_app') ? "+9102228811844" : str_replace(' ', '', $this->appointment->user->phone_number),
            ],
            'notes' => [
                'address' => $this->appointment->address,
            ],
            'callback_url' => url('payments/razorpay/pay-success', ['appointment_id' => $this->appointment->id]),

        );

        if ($this->currency !== 'INR') {
            $fields['display_amount'] = $this->appointment->getTotal();
            $fields['display_currency'] = $this->currency;
        }
        return $fields;
    }

    /**
     * @param int $appointmentId
     * @param int $deliveryAddressId
     * @param Request $request
     * @return JsonResponse|RedirectResponse|Redirector
     */
    public function paySuccess(Request $request, int $appointmentId)
    {
        $data = $request->all();

        $this->appointment = $this->appointmentRepository->findWithoutFail($appointmentId);
        $this->paymentMethodId = 2; // Paypal method
        if (!empty($this->appointment)) {
            if ($request->hasAny(['razorpay_payment_id', 'razorpay_signature'])) {

                $this->createAppointment();

                return redirect(url('payments/razorpay'));
            }
        }
        Flash::error("Error processing RazorPay payment for your appointment");
        return redirect(route('payments.failed'));

    }

}

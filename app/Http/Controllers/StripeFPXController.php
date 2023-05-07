<?php
/*
 * File name: StripeFPXController.php
 * Last modified: 2021.07.24 at 16:24:06
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers;

use Flash;
use Illuminate\Http\Request;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripeFPXController extends ParentAppointmentController
{

    public function __init()
    {
        Stripe::setApiKey(setting('stripe_fpx_secret'));
        Stripe::setClientId(setting('stripe_fpx_key'));
    }

    public function index()
    {
        return view('home');
    }

    public function checkout(Request $request)
    {
        $this->appointment = $this->appointmentRepository->findWithoutFail($request->get('appointment_id'));

        if (empty($this->appointment)) {
            Flash::error("Error processing Stripe FPX payment for your appointment");
            return redirect(route('payments.failed'));
        }
        try {
            $stripeCart = $this->getAppointmentData();
            $intent = PaymentIntent::create($stripeCart);
        } catch (ApiErrorException $e) {
            Flash::error($e->getMessage());
            return redirect(route('payments.failed'));
        }
        return view('payment_methods.stripe_fpx_charge', ['appointment' => $this->appointment, 'intent' => $intent]);
    }

    /**
     * Set cart data for processing payment on Stripe.
     */
    private function getAppointmentData(): array
    {
        $data = [];
        $amount = $this->appointment->getTotal();
        $data['amount'] = (int)($amount * 100);
        $data['payment_method_types'] = ['fpx'];
        $data['currency'] = "myr"; //setting('default_currency_code');
        return $data;
    }

    public function paySuccess(Request $request, int $appointmentId)
    {
        $this->appointment = $this->appointmentRepository->findWithoutFail($appointmentId);

        if (empty($this->appointment)) {
            Flash::error("Error processing Stripe payment for your appointment");
            return redirect(route('payments.failed'));
        } else {
            try {
                $intent = PaymentIntent::retrieve($request->get('payment_intent'));
                if ($intent->status == 'succeeded') {
                    $this->paymentMethodId = 10; // Stripe FPX method id
                    $this->createAppointment();
                    return redirect()->to("payments/stripe-fpx");
                } else {
                    return $this->sendError("Error processing Stripe payment for your appointment");
                }
            } catch (ApiErrorException $e) {
                Flash::error($e->getMessage());
                return redirect(route('payments.failed'));
            }
        }
    }
}

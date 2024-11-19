<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Borrow;

class PaymentController extends Controller
{
    /**
     * showPaymentForm
     *
     * @param  mixed $borrowing
     * @return void
     */
    public function showPaymentForm(Borrow $borrowing)
    {
        $title = $borrowing->book->title;
        $id = $borrowing->id;
        $penalty = $borrowing->total_penalty;

        return view('payments.pay_penalty', compact('borrowing', 'penalty', 'title', 'id'));
    }

    /**
     * processPayment
     *
     * @param  mixed $borrowing
     * @return void
     */
    public function processPayment(Borrow $borrowing)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'inr',
                    'product_data' => [
                        'name' => $borrowing->book->title,
                    ],
                    'unit_amount' => $borrowing->total_penalty * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success', ['borrowing' => $borrowing->id]),
            'cancel_url' => route('payment.cancel', ['borrowing' => $borrowing->id]),
        ]);

        return redirect($session->url);
    }

    /**
     * paymentSuccess
     *
     * @param  mixed $borrowing
     * @return void
     */
    public function paymentSuccess(Borrow $borrowing)
    {
        $borrowing->update(['penalty_paid' => now()]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment successful and penalty cleared!',
        ], 200);
    }

    /**
     * paymentCancel
     *
     * @param  mixed $borrowing
     * @return void
     */
    public function paymentCancel()
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Payment cancelled.',
        ], 400);
    }
}

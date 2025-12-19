<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    /**
     * Show checkout/cart page
     */
    public function index()
    {
        return view('pages.mahasiswa.checkout');
    }

    /**
     * Show payment confirmation page
     */
    public function payment()
    {
        return view('pages.mahasiswa.payment');
    }

    /**
     * Show payment success page
     */
    public function success()
    {
        return view('pages.mahasiswa.payment-success');
    }
}

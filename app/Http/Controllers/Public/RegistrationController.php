<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Registration;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    /**
     * Show the registration form for the specific city.
     */
    public function show($slug)
    {
        $city = City::where('slug', $slug)->firstOrFail();

        return view('public.register', compact('city'));
    }

    /**
     * Store a registration for the specific city.
     */
    public function store(Request $request, $slug)
    {
        $city = City::where('slug', $slug)->firstOrFail();

        // Custom error messages for verification requirements
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'participant_category' => 'required|string|max:255',
            'mentorship_area' => 'required|string|max:255',
            'otp_verified' => 'required|in:1',
            'declaration' => 'required|accepted',
        ], [
            'otp_verified.in' => 'Please verify your Email ID via OTP before submitting.',
            'declaration.accepted' => 'You must accept the voluntary enrollment declaration.',
        ]);

        Registration::create([
            'city_id' => $city->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'participant_category' => $request->participant_category,
            'mentorship_area' => $request->mentorship_area,
            'otp_verified' => true
        ]);

        return redirect()->route('registration.success')
            ->with([
                'success_city' => $city->name,
                'registered_name' => $request->name
            ]);
    }

    /**
     * Show the registration success page.
     */
    public function success()
    {
        return view('public.success');
    }
}

<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

        // Check backend session verification
        if (session('verified_email') !== $request->email) {
            return back()->withErrors([
                'otp_verified' => 'Email verification is invalid or has expired. Please verify your Email ID via OTP.'
            ])->withInput();
        }

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

        // Clear the verified email from session after successful registration
        session()->forget('verified_email');
        session()->forget(['otp_email', 'otp_code', 'otp_expires_at']);

        return redirect()->route('registration.success')
            ->with([
                'success_city' => $city->name,
                'registered_name' => $request->name
            ]);
    }

    /**
     * Generate and send OTP via SendGrid Web API.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = $request->email;
        $otp = (string) rand(100000, 999999);

        // Save in session
        session([
            'otp_email' => $email,
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10)->timestamp
        ]);

        // Sendgrid integration
        $apiKey = env('SENDGRID_API_KEY');
        $fromEmail = env('SENDGRID_FROM_EMAIL');
        $fromName = env('SENDGRID_FROM_NAME', 'ICAI MSME Mentorship');

        if (empty($apiKey) || empty($fromEmail)) {
            // Fallback for testing/unconfigured SendGrid
            \Illuminate\Support\Facades\Log::warning("SendGrid not configured. Generated OTP for {$email}: {$otp}");
            return response()->json([
                'status' => 'success',
                'message' => 'SendGrid is not configured in .env, but OTP generated successfully for testing (checked logs).'
            ]);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.sendgrid.com/v3/mail/send', [
                'personalizations' => [
                    [
                        'to' => [
                            ['email' => $email]
                        ]
                    ]
                ],
                'from' => [
                    'email' => $fromEmail,
                    'name' => $fromName
                ],
                'subject' => 'Your Email Verification OTP Code',
                'content' => [
                    [
                        'type' => 'text/html',
                        'value' => "<p>Dear Participant,</p><p>Your OTP code for ICAI MSME & Startup Mentorship registration is <strong>{$otp}</strong>.</p><p>This code is valid for 10 minutes.</p>"
                    ]
                ]
            ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP sent successfully to your email.'
                ]);
            }

            \Illuminate\Support\Facades\Log::error("SendGrid API Error: " . $response->body());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send OTP email via SendGrid. Please check server logs.'
            ], 500);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("SendGrid Exception: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while sending OTP. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify the entered OTP against session.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'otp' => 'required|string|size:6',
        ]);

        $sessionEmail = session('otp_email');
        $sessionCode = session('otp_code');
        $sessionExpires = session('otp_expires_at');

        if (!$sessionEmail || !$sessionCode || !$sessionExpires) {
            return response()->json([
                'status' => 'error',
                'message' => 'No OTP request found. Please request a new OTP.'
            ], 400);
        }

        if (now()->timestamp > $sessionExpires) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP has expired. Please request a new OTP.'
            ], 400);
        }

        if ($request->email !== $sessionEmail || $request->otp !== $sessionCode) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP code. Please try again.'
            ], 400);
        }

        // Store verification status in session
        session(['verified_email' => $request->email]);

        return response()->json([
            'status' => 'success',
            'message' => 'Email verified successfully.'
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

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSME & Startup Mentorship Registration - {{ $city->name }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brandBlue: '#002B49', // ICAI Dark Navy Blue
                        brandGold: '#D4AF37', // Gold Accent
                        brandOrange: '#F05A28', // OTP Button Orange
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-[#002B49] flex flex-col items-center justify-start">

    <!-- White Header Container -->
    <header class="w-full bg-white py-3 px-4 border-b-4 border-brandGold shadow-md">
        <div class="max-w-3xl mx-auto flex flex-row items-center justify-between gap-2">
            <!-- Left Logo -->
            <img src="{{ asset('logo_0.png') }}" alt="ICAI Logo" class="h-10 sm:h-16 w-auto shrink-0">
            
            <!-- Center Text Branding -->
            <div class="text-center flex-1 min-w-0">
                <h1 class="text-brandBlue font-extrabold text-[8px] sm:text-[13px] leading-tight uppercase tracking-tight">
                    The Institute of Chartered Accountants of India
                </h1>
                <p class="text-slate-500 text-[6px] sm:text-[10px] font-semibold leading-none">(Set up by an Act of Parliament)</p>
                <p class="text-brandBlue font-extrabold text-[8px] sm:text-[12px] leading-tight mt-0.5">
                    MSME & Startup Mentorship 2026 by ICAI
                </p>
                <p class="text-brandBlue font-extrabold text-[8px] sm:text-[12px] leading-tight">
                    Registration Drive @ MSME Mahotsav 2026
                </p>
            </div>
            
            <!-- Right Logo -->
            <img src="{{ asset('logo_1.png') }}" alt="CA India Logo" class="h-8 sm:h-12 w-auto shrink-0">
        </div>
    </header>

    <!-- Main Card Container (With Blue Border Wrapper Frame) -->
    <div class="w-full max-w-xl px-4 py-8 flex-1 flex items-start justify-center">
        <div class="w-full bg-white border border-slate-200 p-5 sm:p-8 rounded-lg shadow-2xl">
            
            <form action="{{ route('registration.store', $city->slug) }}" method="POST" id="registrationForm" onsubmit="return validateFormSubmit(event)">
                @csrf
                
                <!-- Hidden OTP Verified state input -->
                <input type="hidden" name="otp_verified" id="otp_verified" value="{{ old('otp_verified', 0) }}">

                <!-- Step 1: Email ID Authentication -->
                <div class="mb-6">
                    <h3 class="text-sm sm:text-base font-bold text-brandBlue border-b border-slate-100 pb-1.5 mb-3">
                        Step 1: Email ID Authentication
                    </h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-500 mb-1">Email ID* (OTP Verification)</label>
                            <div class="flex gap-2">
                                <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                       class="flex-1 px-3 py-1.5 border border-slate-300 rounded text-slate-800 placeholder-slate-300 text-sm focus:outline-none focus:border-brandBlue transition-all">
                                <button type="button" id="sendOtpBtn" onclick="handleSendOtp()"
                                        class="px-4 py-1.5 bg-[#F05A28] hover:bg-orange-600 text-white rounded text-xs font-bold transition-all shrink-0 flex items-center justify-center">
                                    <span id="otpBtnText">Send OTP</span>
                                </button>
                            </div>
                            @error('email')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- OTP Input Container (Hidden initially) -->
                        <div id="otpInputGroup" class="hidden animate-fadeIn bg-orange-50/50 border border-orange-100 p-3 rounded space-y-2">
                            <label for="otp" class="block text-[11px] font-bold text-orange-800">Enter 6-Digit OTP Sent to Email*</label>
                            <div class="flex gap-2">
                                <input type="text" id="otp" maxlength="6" placeholder="******" 
                                       class="w-28 text-center tracking-widest font-mono font-bold px-2.5 py-1.5 border border-slate-300 rounded bg-white text-sm focus:outline-none focus:border-brandBlue">
                                <button type="button" onclick="handleVerifyOtp()"
                                        class="px-3 py-1.5 bg-brandBlue hover:bg-slate-800 text-white rounded text-[11px] font-bold transition-all">
                                    Verify OTP
                                </button>
                            </div>
                            <p class="text-[9px] text-slate-400">For testing: Enter <strong class="text-slate-600">123456</strong> to verify.</p>
                        </div>

                        <!-- Success Alert for OTP -->
                        <div id="otpSuccessAlert" class="hidden p-2.5 rounded bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs flex items-center gap-1.5">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-500 shrink-0"></i>
                            <span class="font-medium">Email ID verified successfully!</span>
                        </div>
                    </div>
                </div>

                <!-- Fields Wrapper locked until OTP is verified -->
                <div id="formFieldsWrapper" class="opacity-50 pointer-events-none transition-all duration-300">
                    
                    <!-- Step 2: Basic Details -->
                    <div class="mb-6">
                        <h3 class="text-sm sm:text-base font-bold text-brandBlue border-b border-slate-100 pb-1.5 mb-3">
                            Step 2: Basic Details
                        </h3>

                        <div class="space-y-3">
                            <!-- Full Name -->
                            <div>
                                <label for="name" class="block text-xs font-semibold text-slate-500 mb-1">Full Name*</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                       class="w-full px-3 py-1.5 border border-slate-300 rounded text-slate-800 text-sm focus:outline-none focus:border-brandBlue transition-all">
                                @error('name')
                                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Mobile & City (Persistent 2 columns layout on mobile too) -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="phone" class="block text-xs font-semibold text-slate-500 mb-1">Mobile Number*</label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                           class="w-full px-3 py-1.5 border border-slate-300 rounded text-slate-800 text-sm focus:outline-none focus:border-brandBlue transition-all">
                                    @error('phone')
                                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">City*</label>
                                    <input type="text" readonly value="{{ $city->name }}"
                                           class="w-full px-3 py-1.5 border border-slate-200 bg-slate-50 rounded text-slate-500 text-sm focus:outline-none cursor-not-allowed">
                                </div>
                            </div>

                            <!-- Participant Category Dropdown -->
                            <div>
                                <label for="participant_category" class="block text-xs font-semibold text-slate-500 mb-1">Participant Category*</label>
                                <select name="participant_category" id="participant_category"
                                        class="w-full px-3 py-1.5 border border-slate-300 rounded bg-white text-slate-800 text-sm focus:outline-none focus:border-brandBlue transition-all">
                                    <option value="" disabled selected>Select Category</option>
                                    <option value="MSME Entrepreneur" {{ old('participant_category') == 'MSME Entrepreneur' ? 'selected' : '' }}>MSME Entrepreneur</option>
                                    <option value="Startup Founder" {{ old('participant_category') == 'Startup Founder' ? 'selected' : '' }}>Startup Founder</option>
                                    <option value="Chartered Accountant (CA)" {{ old('participant_category') == 'Chartered Accountant (CA)' ? 'selected' : '' }}>Chartered Accountant (CA)</option>
                                    <option value="Student / Aspirant" {{ old('participant_category') == 'Student / Aspirant' ? 'selected' : '' }}>Student / Aspirant</option>
                                    <option value="Business Executive" {{ old('participant_category') == 'Business Executive' ? 'selected' : '' }}>Business Executive</option>
                                    <option value="Other" {{ old('participant_category') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('participant_category')
                                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Mentorship Interest -->
                    <div class="mb-6">
                        <h3 class="text-sm sm:text-base font-bold text-brandBlue border-b border-slate-100 pb-1.5 mb-3">
                            Step 3: Mentorship Interest
                        </h3>
                        <div>
                            <label for="mentorship_area" class="block text-xs font-semibold text-slate-500 mb-1">Area of Mentorship Required*</label>
                            <select name="mentorship_area" id="mentorship_area"
                                    class="w-full px-3 py-1.5 border border-slate-300 rounded bg-white text-slate-800 text-sm focus:outline-none focus:border-brandBlue transition-all">
                                <option value="" disabled selected>Select Area (e.g., Business Growth, Finance, GST, Tech & AI...)</option>
                                <option value="Business Growth & Scaling" {{ old('mentorship_area') == 'Business Growth & Scaling' ? 'selected' : '' }}>Business Growth & Scaling</option>
                                <option value="Fund Raising & Venture Capital" {{ old('mentorship_area') == 'Fund Raising & Venture Capital' ? 'selected' : '' }}>Fund Raising & Venture Capital</option>
                                <option value="GST, Taxation & Auditing" {{ old('mentorship_area') == 'GST, Taxation & Auditing' ? 'selected' : '' }}>GST, Taxation & Auditing</option>
                                <option value="Technology, Digital Transformation & AI" {{ old('mentorship_area') == 'Technology, Digital Transformation & AI' ? 'selected' : '' }}>Technology, Digital Transformation & AI</option>
                                <option value="Legal, IPR & Patent Filing" {{ old('mentorship_area') == 'Legal, IPR & Patent Filing' ? 'selected' : '' }}>Legal, IPR & Patent Filing</option>
                                <option value="Marketing, Branding & Sales" {{ old('mentorship_area') == 'Marketing, Branding & Sales' ? 'selected' : '' }}>Marketing, Branding & Sales</option>
                                <option value="Other" {{ old('mentorship_area') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('mentorship_area')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Step 4: Declaration -->
                    <div class="mb-6">
                        <h3 class="text-sm sm:text-base font-bold text-brandBlue border-b border-slate-100 pb-1.5 mb-3">
                            Step 4: Declaration
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-start gap-2 text-slate-500">
                                <input type="checkbox" name="declaration" id="declaration" required value="1"
                                       class="mt-1 w-4 h-4 rounded text-brandBlue border-slate-300 focus:ring-brandBlue cursor-pointer">
                                <label for="declaration" class="text-[10px] sm:text-xs leading-relaxed cursor-pointer select-none">
                                    I voluntarily enroll in the ICAI MSME & Startup Mentorship Programme 2026, confirming that this registration is unique and has been submitted by me personally. Furthermore, I hereby consent to ICAI using my information for programme communication and Guinness World Records verification.
                                </label>
                            </div>
                            @error('declaration')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button (Centered, Pill shaped) -->
                    <div class="flex justify-center mt-6">
                        <button type="submit"
                                class="px-10 py-2.5 bg-brandBlue hover:bg-slate-800 text-white rounded-full font-bold text-xs sm:text-sm tracking-wide transition-all shadow-md active:scale-[0.98]">
                            Submit Registration
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <!-- Script logic for OTP and verification -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (document.getElementById('otp_verified').value === '1') {
                unlockForm();
            }
            lucide.createIcons();
        });

        function handleSendOtp() {
            const emailInput = document.getElementById('email');
            const sendOtpBtn = document.getElementById('sendOtpBtn');
            const otpBtnText = document.getElementById('otpBtnText');
            
            if (!emailInput.value || !emailInput.checkValidity()) {
                alert('Please enter a valid email address.');
                return;
            }

            sendOtpBtn.disabled = true;
            otpBtnText.innerHTML = "Sending...";
            
            setTimeout(() => {
                document.getElementById('otpInputGroup').classList.remove('hidden');
                sendOtpBtn.classList.remove('bg-[#F05A28]', 'hover:bg-orange-600');
                sendOtpBtn.classList.add('bg-slate-400');
                otpBtnText.innerHTML = "OTP Sent";
            }, 800);
        }

        function handleVerifyOtp() {
            const otpValue = document.getElementById('otp').value.trim();
            
            if (otpValue === '123456') {
                document.getElementById('otp_verified').value = '1';
                document.getElementById('otpInputGroup').classList.add('hidden');
                document.getElementById('sendOtpBtn').classList.add('hidden');
                document.getElementById('otpSuccessAlert').classList.remove('hidden');
                unlockForm();
            } else {
                alert('Invalid OTP. Please enter 123456 to test.');
            }
        }

        function unlockForm() {
            const wrapper = document.getElementById('formFieldsWrapper');
            wrapper.classList.remove('opacity-50', 'pointer-events-none');
            document.getElementById('name').required = true;
            document.getElementById('phone').required = true;
            document.getElementById('participant_category').required = true;
            document.getElementById('mentorship_area').required = true;
        }

        function validateFormSubmit(e) {
            const isVerified = document.getElementById('otp_verified').value === '1';
            if (!isVerified) {
                alert('Please verify your email via OTP first.');
                e.preventDefault();
                return false;
            }
            return true;
        }
    </script>
</body>
</html>

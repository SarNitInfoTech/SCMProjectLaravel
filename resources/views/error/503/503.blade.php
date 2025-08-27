@extends("auth.layout.layout")
@section('bodyContent')

<div class="grid grid-cols-12 authentication p-[3rem] under-maintenance mx-0 text-defaulttextcolor text-defaultsize">
    <div class="xxl:col-span-12">
        <div class="grid grid-cols-12 items-center h-full text-center">
            <div class="xxl:col-span-3 xl:col-span-3 lg:col-span-3 md:col-span-3 sm:col-span-2"></div>

            <div class="xxl:col-span-6 xl:col-span-6 lg:col-span-6 md:col-span-6 sm:col-span-8 col-span-12">
                <div class="mb-2 flex justify-center">
                    <a href="{{ route('dashboard.index') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="authentication-brand h-20 py-3">
                    </a>
                </div>

                <p class="font-semibold text-[0.75rem] mb-1 opacity-[0.4]">STAY TUNED</p>
                <h1 class="font-bold mb-4 text-[2.5rem] dark:text-defaulttextcolor/70">We’ll Be Right Back!</h1>
                <p class="mb-4 text-base">
                    We’re currently performing some scheduled maintenance to improve your experience.  
                    Please check back soon. Thank you for your patience!
                </p>

                <div class="text-sm text-gray-500 mb-6">
                    If you were trying to access a specific page, it might have been moved or temporarily disabled.  
                    You can always return to your dashboard or try again later.
                </div>

                <!-- Countdown -->
                <div class="grid grid-cols-12 mt-6 xxl:gap-y-0 gap-4 mb-[3rem]" id="timer">
                    <!-- JS will inject timer content -->
                </div>

                <!-- Go Back to Dashboard Button -->
                <div class="mt-6">
                    <a href="{{ route('dashboard.index') }}"
                       class="inline-flex items-center px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition duration-200">
                        <i class="ri-arrow-left-line mr-2"></i> Back to Dashboard
                    </a>
                </div>

                <!-- Social Links -->
                <div class="mt-[3rem]">
                    <div class="btn-list">
                        <button class="ti-btn ti-btn-icon bg-primary font-bold me-[0.365rem] text-white">
                            <i class="ri-facebook-line font-bold"></i>
                        </button>
                        <button class="ti-btn ti-btn-icon bg-secondary font-bold me-[0.365rem] text-white">
                            <i class="ri-twitter-x-line"></i>
                        </button>
                        <button class="ti-btn ti-btn-icon bg-warning font-bold me-[0.365rem] text-white">
                            <i class="ri-instagram-line font-bold"></i>
                        </button>
                        <button class="ti-btn ti-btn-icon bg-success font-bold me-[0.365rem] text-white">
                            <i class="ri-github-line font-bold"></i>
                        </button>
                        <button class="ti-btn ti-btn-icon bg-danger font-bold text-white">
                            <i class="ri-youtube-line font-bold"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="xxl:col-span-3 xl:col-span-3 lg:col-span-3 md:col-span-3 sm:col-span-2"></div>
        </div>
    </div>
</div>

<script>
    const countdownDate = new Date("2025-12-31T23:59:59").getTime();

    const timer = setInterval(function () {
        const now = new Date().getTime();
        const distance = countdownDate - now;

        if (distance < 0) {
            clearInterval(timer);
            return;
        }

        document.getElementById("timer").innerHTML = `
            <div class="xxl:col-span-3 col-span-6">
                <div class="p-3 border-2 border-dashed rounded-md">
                    <p class="mb-1 text-[0.75rem] opacity-[0.5]">DAYS</p>
                    <h4 class="font-semibold mb-0 text-[1.5rem]">${Math.floor(distance / (1000 * 60 * 60 * 24))}</h4>
                </div>
            </div>
            <div class="xxl:col-span-3 col-span-6">
                <div class="p-3 border-2 border-dashed rounded-md">
                    <p class="mb-1 text-[0.75rem] opacity-[0.5]">HOURS</p>
                    <h4 class="font-semibold mb-0 text-[1.5rem]">${Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))}</h4>
                </div>
            </div>
            <div class="xxl:col-span-3 col-span-6">
                <div class="p-3 border-2 border-dashed rounded-md">
                    <p class="mb-1 text-[0.75rem] opacity-[0.5]">MINUTES</p>
                    <h4 class="font-semibold mb-0 text-[1.5rem]">${Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))}</h4>
                </div>
            </div>
            <div class="xxl:col-span-3 col-span-6">
                <div class="p-3 border-2 border-dashed rounded-md">
                    <p class="mb-1 text-[0.75rem] opacity-[0.5]">SECONDS</p>
                    <h4 class="font-semibold mb-0 text-[1.5rem]">${Math.floor((distance % (1000 * 60)) / 1000)}</h4>
                </div>
            </div>
        `;
    }, 1000);
</script>

@endsection

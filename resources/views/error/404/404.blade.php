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

                <p class="font-semibold text-[0.75rem] mb-1 opacity-[0.4]">404 — PAGE NOT FOUND</p>
                <h1 class="font-bold mb-4 text-[2.5rem] dark:text-defaulttextcolor/70">Oops! We couldn’t find that page.</h1>
                <p class="mb-4 text-base">
                    The page you’re looking for might have been removed, renamed, or is temporarily unavailable.
                    <br>Please check the URL or return to the dashboard.
                </p>

                <!-- Back Button -->
                <div class="mt-6">
                    <a href="{{ route('dashboard.index') }}"
                       class="inline-flex items-center px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition duration-200">
                        <i class="ri-arrow-left-line mr-2"></i> Back to Dashboard
                    </a>
                </div>

                <!-- Social Icons -->
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

@endsection

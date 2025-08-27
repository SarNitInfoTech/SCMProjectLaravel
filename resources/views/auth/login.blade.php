@extends("auth.layout.layout")

@section("bodyContent")
<div class="container">
    <div class="grid grid-cols-12 authentication authentication-basic items-center h-full text-defaultsize text-defaulttextcolor">
        <div class="xxl:col-span-4 xl:col-span-4 lg:col-span-4 md:col-span-3 sm:col-span-2"></div>
        <div class="xxl:col-span-4 xl:col-span-4 lg:col-span-4 md:col-span-6 sm:col-span-8 col-span-12">
            <div class="my-[2.5rem] flex justify-center">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="NITRA Logo" class="desktop-logo h-12">
                </a>
            </div>
            <div class="box">
                
                <div class="box-body !p-[3rem]">
                    <p class="h5 font-semibold mb-2 text-center !text-defaulttextcolor dark:!text-defaulttextcolor/85">NITRA Login</p>
                    <p class="mb-4 text-[#8c9097] opacity-[0.7] font-normal text-center">Welcome back! Please log in to continue.</p>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="grid grid-cols-12 gap-y-4">
                            <div class="xl:col-span-12 col-span-12">
                                <label for="email" class="form-label text-default">Email Address</label>
                                <input type="email" class="form-control form-control-lg w-full !rounded-md" id="email" name="email" placeholder="you@example.com" required>
                            </div>
                            

                            <div class="xl:col-span-12 col-span-12">
                                <label for="password" class="form-label text-default block">Password
                                    <a href="#" class="float-right text-danger">Forgot password?</a>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg !rounded-tl-md !rounded-bl-md" id="password" name="password" placeholder="********" required>
                                    <button class="ti-btn ti-btn-light !rounded-tl-none !rounded-bl-none !mb-0 !border !border-s-0 !border-defaultborder/10" type="button" onclick="createpassword('password',this)">
                                        <i class="ri-eye-off-line align-middle"></i>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <div class="form-check flex items-center gap-2">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                        <label class="form-check-label text-[#8c9097] font-normal" for="remember">
                                            Remember Me
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="xl:col-span-12 col-span-12 grid">
                                <button type="submit" class="ti-btn ti-btn-lg bg-primary !border-0 text-white !font-medium w-full">
                                    Sign In
                                </button>
                            </div>
                        </div>
                        
                    </form>
@include("common.toast.commonToast")
                    {{-- <div class="text-center">
                        <p class="text-[0.75rem] text-[#8c9097] mt-4">Don’t have an account?
                            <a href="{{ route('register') }}" class="text-primary">Register</a>
                        </p>
                    </div> --}}

                    {{-- <div class="text-center my-4 authentication-barrier">
                        <span>OR</span>
                    </div> --}}

                    {{-- <div class="btn-list text-center">
                        <button class="ti-btn ti-btn-icon ti-btn-light me-[0.365rem]">
                            <i class="ri-facebook-line font-bold text-dark opacity-[0.7]"></i>
                        </button>
                        <button class="ti-btn ti-btn-icon ti-btn-light me-[0.365rem]">
                            <i class="ri-google-line font-bold text-dark opacity-[0.7]"></i>
                        </button>
                        <button class="ti-btn ti-btn-icon ti-btn-light">
                            <i class="ri-twitter-x-line font-bold text-dark opacity-[0.7]"></i>
                        </button>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="xxl:col-span-4 xl:col-span-4 lg:col-span-4 md:col-span-3 sm:col-span-2"></div>
    </div>
</div>

<script>
    function createpassword(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('i');

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("ri-eye-off-line");
            icon.classList.add("ri-eye-line");
        } else {
            input.type = "password";
            icon.classList.remove("ri-eye-line");
            icon.classList.add("ri-eye-off-line");
        }
    }
</script>
@endsection

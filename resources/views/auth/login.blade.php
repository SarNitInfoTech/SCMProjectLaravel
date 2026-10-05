@extends("auth.layout.layout")

@section("bodyContent")
<style>
    /* Full view reset for auth layout */
    html, body {
        min-height: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        background-color: #ebf3fa !important;
        background: radial-gradient(circle at 50% 25%, #f4f8fd 0%, #e2ecf7 50%, #d5e3f2 100%) !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }

    .page {
        min-height: 100vh !important;
        height: auto !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
        padding: 1.5rem 1rem !important;
        box-sizing: border-box !important;
    }

    /* Floating symmetrical Card */
    .auth-card-container {
        width: 100%;
        max-width: 440px;
        background: #ffffff !important;
        border-radius: 20px !important;
        box-shadow: 0 20px 45px -10px rgba(15, 34, 70, 0.14), 0 0 1px 1px rgba(0, 0, 0, 0.04) !important;
        overflow: hidden;
        border-top: 4px solid #1d4ed8 !important;
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
        margin: auto 0;
    }

    .auth-card-body {
        padding: 1.75rem 2rem 1.5rem 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        box-sizing: border-box;
    }

    /* Logo card box */
    .nitra-logo-box {
        width: 90px;
        height: 90px;
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        margin-bottom: 1.15rem;
    }

    .nitra-logo-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    /* Pill badge */
    .portal-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 9999px;
        margin-bottom: 1rem;
    }

    .portal-badge-dot {
        width: 6px;
        height: 6px;
        background: #2563eb;
        border-radius: 50%;
    }

    /* Titles */
    .title-pms {
        font-size: 1.75rem !important;
        font-weight: 900 !important;
        color: #000000 !important;
        line-height: 1.15 !important;
        margin: 0 0 0.25rem 0 !important;
        text-align: center !important;
        letter-spacing: -0.02em !important;
    }

    .subtitle-system {
        font-size: 0.82rem !important;
        font-weight: 700 !important;
        color: #1d4ed8 !important;
        margin: 0 0 0.35rem 0 !important;
        text-align: center !important;
        letter-spacing: 0.01em !important;
    }

    .instruction-text {
        font-size: 0.73rem !important;
        font-weight: 500 !important;
        color: #64748b !important;
        margin: 0 0 1.25rem 0 !important;
        text-align: center !important;
        line-height: 1.35 !important;
    }

    /* Form styling */
    .auth-form {
        width: 100%;
    }

    .form-group-item {
        margin-bottom: 1rem;
        width: 100%;
        display: flex;
        flex-direction: column;
    }

    .form-group-item label {
        font-size: 0.74rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.4rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .req-star {
        color: #ef4444;
        margin-left: 2px;
    }

    .forgot-link {
        font-size: 0.73rem;
        font-weight: 600;
        color: #2563eb;
        text-decoration: none;
    }

    .forgot-link:hover {
        text-decoration: underline;
    }

    /* Input Field Container */
    .input-field-wrapper {
        position: relative;
        width: 100%;
        display: flex;
        align-items: center;
    }

    .input-field-wrapper .field-icon {
        position: absolute;
        left: 14px;
        color: #94a3b8;
        font-size: 1.15rem;
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }

    .input-field-wrapper input {
        width: 100% !important;
        height: 44px !important;
        background-color: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding-left: 42px !important;
        padding-right: 42px !important;
        font-size: 0.88rem !important;
        color: #0f172a !important;
        box-sizing: border-box !important;
        transition: all 0.2s ease !important;
    }

    .input-field-wrapper input:focus {
        background-color: #ffffff !important;
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
        outline: none !important;
    }

    .input-field-wrapper input::placeholder {
        color: #94a3b8 !important;
        font-size: 0.85rem !important;
    }

    .toggle-pass-btn {
        position: absolute;
        right: 10px;
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
        transition: color 0.15s ease;
    }

    .toggle-pass-btn:hover {
        color: #334155;
    }

    /* Checkbox */
    .remember-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: -0.25rem;
        margin-bottom: 1.35rem;
    }

    .remember-wrap input[type="checkbox"] {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        border: 1px solid #cbd5e1;
        cursor: pointer;
        accent-color: #2563eb;
    }

    .remember-wrap label {
        font-size: 0.78rem;
        font-weight: 500;
        color: #64748b;
        cursor: pointer;
        user-select: none;
    }

    /* Submit Button */
    .btn-submit-action {
        width: 100% !important;
        height: 48px !important;
        background: #1d4ed8 !important;
        border: none !important;
        border-radius: 12px !important;
        color: #ffffff !important;
        font-size: 0.95rem !important;
        font-weight: 700 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 10px !important;
        cursor: pointer !important;
        box-shadow: 0 4px 14px rgba(29, 78, 216, 0.35) !important;
        transition: all 0.2s ease !important;
        text-decoration: none !important;
    }

    .btn-submit-action:hover {
        background: #1e40af !important;
        box-shadow: 0 6px 20px rgba(29, 78, 216, 0.45) !important;
        transform: translateY(-1px);
    }

    .btn-submit-action:active {
        transform: translateY(0);
    }

    /* Contact Admin Help */
    .contact-help-text {
        font-size: 0.8rem;
        color: #475569;
        font-weight: 500;
        margin-top: 2rem;
        margin-bottom: 0.25rem;
        text-align: center;
    }

    .contact-help-text a {
        color: #1d4ed8;
        font-weight: 700;
        text-decoration: none;
        margin-left: 2px;
    }

    .contact-help-text a:hover {
        text-decoration: underline;
    }

    /* Bottom Security Strip */
    .security-strip {
        width: 100%;
        background-color: #f8fafc;
        border-top: 1px solid #f1f5f9;
        padding: 0.95rem 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-sizing: border-box;
    }

    .security-strip i {
        color: #059669;
        font-size: 1.1rem;
        font-weight: bold;
    }

    .security-strip span {
        font-size: 0.72rem;
        font-weight: 800;
        color: #475569;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    /* Copyright outside card */
    .copyright-footer {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 1.5rem;
        text-align: center;
    }
</style>

<!-- Symmetrical Card -->
<div class="auth-card-container">
    <div class="auth-card-body">
        <!-- Logo Box -->
        <div class="nitra-logo-box">
            <a href="{{ url('/') }}" style="display: flex; width: 100%; height: 100%; align-items: center; justify-content: center;">
                <img src="{{ asset('images/logo.png') }}" alt="NITRA Logo">
            </a>
        </div>

        <!-- Portal Badge -->
        <div class="portal-badge">
            <span class="portal-badge-dot"></span>
            NITRA Institutional Portal
        </div>

        <!-- PMS Title & Subtitle -->
        <h1 class="title-pms">PMS</h1>
        <p class="subtitle-system">Purchase &amp; Inventory Management System</p>
        <p class="instruction-text">Enter your institutional credentials to access your account</p>

        <!-- Form -->
        <form method="POST" action="{{ route('login.submit') }}" class="auth-form">
            @csrf
            
            <!-- Email -->
            <div class="form-group-item">
                <label for="email">
                    <span>Institutional Email <span class="req-star">*</span></span>
                </label>
                <div class="input-field-wrapper">
                    <span class="field-icon"><i class="ri-mail-line"></i></span>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        placeholder="name@nitratextile.org" 
                        required 
                        autofocus
                    >
                </div>
                @error('email')
                    <span style="color: #ef4444; font-size: 0.75rem; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group-item">
                <label for="password">
                    <span>Password <span class="req-star">*</span></span>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    @else
                        <a href="#" class="forgot-link">Forgot password?</a>
                    @endif
                </label>
                <div class="input-field-wrapper">
                    <span class="field-icon"><i class="ri-lock-2-line"></i></span>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••••••" 
                        required
                    >
                    <button type="button" class="toggle-pass-btn" onclick="createpassword('password', this)" title="Toggle visibility">
                        <i class="ri-eye-off-line"></i>
                    </button>
                </div>
                @error('password')
                    <span style="color: #ef4444; font-size: 0.75rem; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Remember me -->
            <div class="remember-wrap">
                <input 
                    type="checkbox" 
                    name="remember" 
                    id="remember" 
                    {{ old('remember') ? 'checked' : '' }}
                >
                <label for="remember">Remember this device</label>
            </div>

            <!-- Submit button -->
            <button type="submit" class="btn-submit-action">
                <span>Sign In to PMS</span>
                <i class="ri-arrow-right-line" style="font-size: 1.15rem; font-weight: bold;"></i>
            </button>
        </form>

        @include("common.toast.commonToast")

        <!-- Subtle Divider & Help / Admin Contact -->
        <div style="width: 100%; border-top: 1px solid #f1f5f9; margin-top: 28px; padding-top: 22px; text-align: center;">
            <p style="font-size: 0.85rem; color: #475569; font-weight: 500; margin: 0; line-height: 1.4;">
                Don’t have an account or need access? 
                <a href="mailto:admin@nitratextile.org" style="color: #2563eb; font-weight: 700; text-decoration: none; margin-left: 4px;">
                    Contact System Admin
                </a>
            </p>
        </div>
    </div>

    <!-- Security Footer Strip -->
    <div style="width: 100%; background-color: #f8fafc; border-top: 1px solid #f1f5f9; padding: 16px 20px; display: flex; align-items: center; justify-content: center; gap: 8px; box-sizing: border-box;">
        <svg style="width: 18px; height: 18px; color: #059669; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            <polyline points="9 12 11 14 15 10"></polyline>
        </svg>
        <span style="font-size: 0.76rem; font-weight: 800; color: #475569; letter-spacing: 0.05em; text-transform: uppercase;">
            AUTHORIZED PERSONNEL &amp; RESEARCHER ACCESS ONLY
        </span>
    </div>
</div>

<!-- Copyright -->
<div class="copyright-footer">
    &copy; {{ date('Y') }} Northern India Textile Research Association (NITRA)
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

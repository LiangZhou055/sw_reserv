@extends('layouts.frontend')
@section('content')
<div class="col-12 px-0">
    <div
        class="login-left-section d-flex align-items-center justify-content-center"
        style="background: radial-gradient(circle at 15% 20%, rgba(99,102,241,0.35), transparent 40%), radial-gradient(circle at 85% 10%, rgba(56,189,248,0.30), transparent 38%), linear-gradient(140deg, #020617 0%, #0f172a 50%, #111827 100%);"
    >
        <div class="form-container" style="max-width: 460px; width: 100%; padding: 20px;">
            <div
                style="background: rgba(255, 255, 255, 0.95); border: 1px solid rgba(148, 163, 184, 0.25); border-radius: 20px; padding: 28px; box-shadow: 0 24px 60px rgba(2, 6, 23, 0.45);"
            >
                <div class="text-center mb-3">
                    <h4 class="mb-1" style="font-weight: 700; color: #0f172a;">Device Notice</h4>
                    <p class="mb-0" style="font-size: 13px; color: #64748b;">Device authorization is required before access.</p>
                </div>
                <hr>

                <div class="px-2 py-3 text-center" style="color: #334155; font-size: 15px;">
                    @if($new_device)
                        <span>Device is not registered, please register your device.</span>
                    @else
                        <span>Device is waiting for approval, please contact the provider.</span>
                    @endif
                </div>

                <div class="text-center pt-3">
                    <form action="{{ route('registration.verify') }}" method="POST">
                        @csrf
                        <input type="hidden" name="sn" value="{{ $sn }}">
                        <input type="hidden" name="store" value="{{ $storeCode ?? '' }}">

                        @if($new_device)
                            <button type="submit" class="shadow btn btn--info px-5 py-2 text-light">@lang('Register')</button>
                        @else
                            <button type="submit" class="shadow btn btn-primary px-5 py-2 text-light">@lang('Refresh')</button>
                        @endif
                    </form>
                </div>
            </div>

            <div class="text-center pt-4" style="font-size: 12px; color: rgba(226, 232, 240, 0.9);">
                <span>&copy {{ date('Y') }}, Sayweb Inc. </span>
            </div>
        </div>
    </div>
</div>
@endsection

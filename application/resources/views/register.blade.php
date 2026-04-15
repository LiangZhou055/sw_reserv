@extends('layouts.frontend')
@section('content')
 <div class="col-12 col-md-12 px-0">
        <div class="login-left-section d-flex align-items-center justify-content-center">
            <div class="form-container">
                <div style="padding-bottom: 20px;">
                    <div class="mb-3  text-center">
                        <h4><span class="site--title">Notice</span></h4>
                    </div>
                    <hr>
                    
                    <div style="padding:20px">
                    @if($new_device) 
                        <span>Device is not registered, please register your device</span>
                    @else
                        <span>Device is waiting for approval, please contact the provider</span>
                    @endif
                    </div> 
                    
                    <div style="text-align:center">                        
                        <img src="{{ asset('assets/images/logo.png') }}" style="height:120px; width:auto;"><br>
                    </div>
                    <div class="text-center pt-3 h-30">
                        <form action="{{route('registration.verify')}}" method="POST">
                            @csrf                
                            <input type="hidden" name="sn" value="{{$sn}}">

                            @if($new_device) 
                            <button type="submit" class="shadow btn btn--info w-50 mt-2 text-light">@lang('Register')</button>                            
                            @else
                            <button type="submit" class="shadow btn btn-primary w-50 mt-2 text-light">@lang('Refresh')</button>
                            @endif

                        </form>
                    </div>
                </div>
                <div class="text-center pt-5">&copy {{ date('Y') }}, Sayweb.ca All rights reserved</div>
               
        </div>
    </div>
</div>
@endsection

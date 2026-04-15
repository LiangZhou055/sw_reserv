<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('assets/global/css/line-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/global/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/global/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/global/css/toastr.css')}}">
    <link rel="stylesheet" href="{{asset('assets/dashboard/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/dashboard/css/responsive.css')}}">
    <link rel="stylesheet" href="{{asset('assets/dashboard/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/dashboard/flag-icons/flag-icons.css')}}"> 
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.2.19/build/css/intlTelInput.css">
    <link rel="stylesheet" href="{{asset('assets/customize.css')}}"> <!--sayweb -->

    @stack('style-include')
    @stack('stylepush')
    
</head>

<body>
    @yield('content')
    <script src="{{asset('assets/global/js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{asset('assets/global/js/all.min.js')}}"></script>
    <script src="{{asset('assets/global/js/toastr.js')}}"></script>
    <script src="{{asset('assets/global/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/dashboard/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/dashboard/js/parsley.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.2.19/build/js/intlTelInput.min.js"></script>
    @include('partials.notify')
    @stack('script-include')
    @stack('scriptpush')
</body>
</html>

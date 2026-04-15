<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="{{asset('assets/global/css/line-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/global/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/frontend/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/frontend/css/responsive.css')}}">
    <link rel="stylesheet" href="{{asset('assets/global/css/toastr.css')}}">
</head>
<body>
    <div class="login-page-container">
        <div class="container-fluid p-0">
            <div class="row responsive-shadow overflow-hidden">
            @yield('content')
        </div>
    </div>
</div>
<script src="{{asset('assets/global/js/jquery-3.6.0.min.js')}}"></script>
<script src="{{asset('assets/global/js/all.min.js')}}"></script>
<script src="{{asset('assets/global/js/toastr.js')}}"></script>
<script src="{{asset('assets/global/js/bootstrap.bundle.min.js')}}"></script>
@include('partials.notify')
</body>
</html>

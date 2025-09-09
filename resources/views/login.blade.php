<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Login - Talkamos</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

     <!-- BEGIN: Vendor CSS-->
     <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendors.min.css')}}">
     <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/charts/apexcharts.css')}}">
     <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.min.css')}}">
     <!-- END: Vendor CSS-->

     <!-- BEGIN: Theme CSS-->
     <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/bootstrap.css')}}">
     <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/bootstrap-extended.css')}}">
     <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/colors.css')}}">
     <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/components.css')}}">
     <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/dark-layout.css')}}">
     <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/bordered-layout.css')}}">
     <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/semi-dark-layout.css')}}">

     <!-- BEGIN: Page CSS-->
     <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
     <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/dashboard-ecommerce.css')}}">
     <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/charts/chart-apex.css')}}">
     <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/extensions/ext-component-toastr.css')}}">
     <!-- END: Page CSS-->

     <!-- BEGIN: Custom CSS-->
     <link rel="stylesheet" type="text/css" href="{{asset('')}}assets/css/style.css">
     <!-- END: Custom CSS-->


</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="continer">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="content-wrapper">
                        <div class="content-header row">
                        </div>
                        <div class="content-body">
                            <div class="auth-wrapper auth-v1 px-2">
                                <div class="auth-inner py-2">
                                    <!-- Login v1 -->
                                    <div class="card mb-0">
                                        <div class="card-body">
                                            <a href="javascript:void(0);" class="brand-logo">
                                                <img src="{{url('logo/gold__2.png')}}" alt="">
                                                <h2 class="brand-text text-primary ml-1">Talkamos</h2>
                                            </a>

                                            <h4 class="card-title mb-1">Welcome to Talkamos! 👋</h4>
                                            <p class="card-text mb-2">Please sign-in to your account and start the adventure</p>

                                            @include('admin.flash')
                                            <form class="auth-login-form mt-2" action="{{url('/admin/login')}}" method="POST">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="login-email" class="form-label">Email</label>
                                                    <input type="text" class="form-control" id="login-email" name="email" placeholder="john@example.com" aria-describedby="login-email" tabindex="1" autofocus />
                                                </div>

                                                <div class="form-group">
                                                    <div class="input-group input-group-merge form-password-toggle">
                                                        <input type="password" class="form-control form-control-merge" id="login-password" name="password" tabindex="2" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="login-password" />
                                                        <div class="input-group-append">
                                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="remember-me" tabindex="3" />
                                                        <label class="custom-control-label" for="remember-me"> Remember Me </label>
                                                    </div>
                                                </div>
                                                <button class="btn btn-primary btn-block" tabindex="4">Sign in</button>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- /Login v1 -->
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>

    </div>
    <!-- END: Content-->



    <!-- BEGIN: Vendor JS-->
    <script src="{{asset('app-assets/vendors/js/vendors.min.js')}}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="{{asset('app-assets/vendors/js/charts/apexcharts.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{asset('app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{asset('app-assets/js/core/app.js')}}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="{{asset('app-assets/js/scripts/pages/dashboard-ecommerce.js')}}"></script>
    <!-- END: Page JS-->

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>
</body>
<!-- END: Body-->

</html>

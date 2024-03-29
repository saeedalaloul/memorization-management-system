<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="Al-ansar Center"/>
    <meta name="description" content="مركز الأنصار لتحفيظ القرآن الكريم والسنة النبوية"/>
    <meta name="author" content="#"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>مركز الأنصار لتحفيظ القرآن الكريم والسنة النبوية</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset('assets/images/favicon.ico',true)}}"/>

    <!-- Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Poppins:200,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900">
    <!-- css -->
    <link href="{{ URL::asset('assets/css/rtl.css',true) }}" rel="stylesheet">


    <style type="text/css">/* Chart.js */
        @-webkit-keyframes chartjs-render-animation {
            from {
                opacity: 0.99
            }
            to {
                opacity: 1
            }
        }

        @keyframes chartjs-render-animation {
            from {
                opacity: 0.99
            }
            to {
                opacity: 1
            }
        }

        .chartjs-render-monitor {
            -webkit-animation: chartjs-render-animation 0.001s;
            animation: chartjs-render-animation 0.001s;
        }</style>

    <style type="text/css">.jqstooltip {
            position: absolute;
            left: 0px;
            top: 0px;
            visibility: hidden;
            background: rgb(0, 0, 0) transparent;
            background-color: rgba(0, 0, 0, 0.6);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000);
            -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000)";
            color: white;
            font: 10px arial, san serif;
            text-align: left;
            white-space: nowrap;
            padding: 5px;
            border: 1px solid white;
            z-index: 10000;
        }

        .jqsfield {
            color: white;
            font: 10px arial, san serif;
            text-align: left;
        }</style>

</head>

<body>

<div class="wrapper">

    <!--=================================
preloader -->

    <div id="pre-loader">
        <img src="{{URL::asset('assets/images/pre-loader/loader-01.svg',true)}}" alt="">
    </div>

    <!--=================================
preloader -->

    <!--=================================
login-->
    <section class="height-100vh d-flex align-items-center page-section-ptb login"
             style="background-image: url('{{ asset('assets/images/sativa.png')}}');">
        <div class="container">
            <div class="row justify-content-center g-0 vertical-align">
                <div class="col-lg-4 col-md-6 login-fancy-bg bg"
                     style="background-image: url({{URL::asset('assets/images/login-inner-bg.jpg',true)}});">
                    <div class="login-fancy">
                        <h2 class="text-white mb-20">مركز الأنصار</h2>
                        <p class="mb-20 text-white">مركز الأنصار لتحفيظ القرآن الكريم والسنة النبوية مسجد بسيسو.</p>
                        <ul class="list-unstyled  pos-bot pb-30">
                            <li class="list-inline-item"><a class="text-white" href="#"> Terms of Use</a></li>
                            <li class="list-inline-item"><a class="text-white" href="#"> Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 bg-white">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="login-fancy pb-40 clearfix">
                        <h3 class="mb-30">تسجيل الدخول إلى لوحة التحكم</h3>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="section-field mb-20">
                                <label class="mb-10" for="name">البريدالالكتروني أو رقم الجوال أو رقم الهوية* </label>
                                <input id="email" type="text"
                                       class="form-control @error('email') is-invalid @enderror" name="email"
                                       value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="section-field mb-20">
                                <label class="mb-10" for="Password">كلمة المرور* </label>
                                <input id="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror" name="password" required>

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="section-field">
                                <div class="remember-checkbox mb-30">
                                    <input type="checkbox" class="form-control" name="remember"
                                           id="remember">
                                    <label for="remember"> تذكرني</label>
                                    <a href="{{route('password.request')}}" class="float-sm-end d-block mt-1 mt-sm-0">هل
                                        نسيت كلمةالمرور ؟</a>
                                </div>
                            </div>
                            <button class="button">
                                <span>دخول</span>
                                <i class="fa fa-check"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- jquery -->
<script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js',true) }}"></script>
<!-- plugins-jquery -->
<script src="{{ URL::asset('assets/js/plugins-jquery.js',true) }}"></script>
<!-- plugin_path -->
<script>
    var plugin_path = 'js/';
</script>

<!-- chart -->
<script src="{{ URL::asset('assets/js/chart-init.js',true) }}"></script>
<!-- calendar -->
<script src="{{ URL::asset('assets/js/calendar.init.js',true) }}"></script>
<!-- charts sparkline -->
<script src="{{ URL::asset('assets/js/sparkline.init.js',true) }}"></script>
<!-- charts morris -->
<script src="{{ URL::asset('assets/js/morris.init.js',true) }}"></script>
<!-- datepicker -->
<script src="{{ URL::asset('assets/js/datepicker.js',true) }}"></script>
<!-- sweetalert2 -->
<script src="{{ URL::asset('assets/js/sweetalert2.js',true) }}"></script>
<!-- toastr -->
@yield('js')
<script src="{{ URL::asset('assets/js/toastr.js',true) }}"></script>
<!-- validation -->
<script src="{{ URL::asset('assets/js/validation.js',true) }}"></script>
<!-- lobilist -->
<script src="{{ URL::asset('assets/js/lobilist.js',true) }}"></script>
<!-- custom -->
<script src="{{ URL::asset('assets/js/custom.js',true) }}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/nicescroll/jquery.nicescroll.js',true)}}"></script>
</body>

</html>

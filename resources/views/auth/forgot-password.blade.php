<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="HTML5 Template"/>
    <meta name="description" content="Webmin - Bootstrap 4 & Angular 5 Admin Dashboard Template"/>
    <meta name="author" content="#"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>نسيت كلمة المرور</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset('assets/images/favicon.ico',true)}}"/>

    <!-- Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Poppins:200,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900">
    <!-- css -->
    <link href="{{ URL::asset('assets/css/rtl.css',true) }}" rel="stylesheet">
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
                        <h2 class="text-white mb-20">{{env('app_name')}}</h2>
                        <p class="mb-20 text-white">أدخل عنوان بريدك الإلكتروني وسنرسل لك رابطًا لإعادة تعيين كلمة مرورك.</p>
                        <ul class="list-unstyled  pos-bot pb-30">
                            <li class="list-inline-item"><a class="text-white" href="#"> Terms of Use</a></li>
                            <li class="list-inline-item"><a class="text-white" href="#"> Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 bg-white">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="login-fancy pb-40 clearfix">
                        <h3 class="mb-30">هل نسيت كلمة المرور؟</h3>
                        <div class="section-field mb-20">
                            <p>أدخل عنوان بريدك الإلكتروني وسنرسل لك رابطًا لإعادة تعيين كلمة مرورك.</p>
                        </div>
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="section-field mb-20">
                                <label class="mb-10" for="name">البريدالالكتروني* </label>
                                <input id="email" type="text"
                                       class="form-control @error('email') is-invalid @enderror" name="email"
                                       value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <button class="button">
                                <span>إرسال الرابط</span>
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
<!-- custom -->
<script src="{{ URL::asset('assets/js/custom.js',true) }}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/nicescroll/jquery.nicescroll.js',true)}}"></script>
</body>

</html>

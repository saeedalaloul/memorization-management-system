<!-- Title -->
<title>@yield("title")</title>

<!-- Favicon -->
<link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico',true) }}" type="image/x-icon"/>

<!-- Font -->
<link rel="stylesheet"
      href="https://fonts.googleapis.com/css?family=Poppins:200,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
      integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

<link href="{{ URL::asset('css/wizard.css',true) }}" rel="stylesheet" id="bootstrap-css">

@yield('css')
<!--- Style css -->
<link href="{{ URL::asset('assets/css/style.css',true) }}" rel="stylesheet">

<!--- Style css -->
@if (App::getLocale() == 'en')
    <link href="{{ URL::asset('assets/css/ltr.css',true) }}" rel="stylesheet">
@else
    <link href="{{ URL::asset('assets/css/rtl.css',true) }}" rel="stylesheet">
@endif

<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>

<script>
    window.OneSignal = window.OneSignal || [];
    OneSignal.push(function () {
        OneSignal.init({
            appId: "c99992cc-e40f-46d1-8f7c-a5e4efd99c88",
        });

        OneSignal.setExternalUserId({{auth()->id() != null ? auth()->id() : null}});
    });
</script>

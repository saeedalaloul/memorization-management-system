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
<meta name="csrf-token" content="{{csrf_token()}}"/>

@yield('css')
<!--- Style css -->
<link href="{{ URL::asset('assets/css/style.css',true) }}" rel="stylesheet">

<!--- Style css -->
@if (App::getLocale() == 'en')
    <link href="{{ URL::asset('assets/css/ltr.css',true) }}" rel="stylesheet">
@else
    <link href="{{ URL::asset('assets/css/rtl.css',true) }}" rel="stylesheet">
@endif

<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase.js"></script>
<script>
    const firebaseConfig = {
        apiKey: "AIzaSyCnDkYAfzlrYwjXVG9Csx2OYZ8tJzkPXvM",
        authDomain: "alansarcenter-c93d5.firebaseapp.com",
        databaseURL: "https://alansarcenter-c93d5.firebaseio.com",
        projectId: "alansarcenter-c93d5",
        storageBucket: "alansarcenter-c93d5.appspot.com",
        messagingSenderId: "520516617212",
        appId: "1:520516617212:web:2e876da05504afd12ae879",
        measurementId: "G-3ER6SP33B6"
    };

    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();


    messaging
        .requestPermission()
        .then(function () {
            return messaging.getToken()
        })
        .then(function (response) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route("store.token") }}',
                type: 'POST',
                data: {
                    token: response
                },
                dataType: 'JSON',
                success: function () {
                },
                error: function (error) {
                    toastr.options =
                        {
                            "closeButton": false,
                            "progressBar": true
                        }
                    toastr.error(error);
                },
            });
        }).catch(function (error) {
        toastr.options =
            {
                "closeButton": false,
                "progressBar": true
            }
        toastr.error(error);
    });

    // messaging.onMessage(function (payload) {
    //     const title = payload.notification.title;
    //     const options = {
    //         body: payload.notification.body,
    //         icon: payload.notification.icon,
    //         requireInteraction: true,
    //         dir:'rtl',
    //     };
    //     new Notification(title, options);
    // });
</script>

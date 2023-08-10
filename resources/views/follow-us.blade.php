<!DOCTYPE html>
<html lang="ar">
<head>
    <title>تابعنا على وسائل التواصل الإجتماعي</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="Al-ansar Center"/>
    <meta name="description" content="مركز الأنصار لتحفيظ القرآن الكريم والسنة النبوية"/>
    <meta name="author" content="مركز الأنصار لتحفيظ القرآن الكريم والسنة النبوية"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900&display=swap" rel="stylesheet">
    @include('layouts.head')
</head>
<body>
<div class="follow-widget">
    <h2>تابعنا على</h2>
    <p>ابق على اطلاع بأخر التحديثات عبر وسائل التواصل الاجتماعي</p>
    <ul>
        <li><a target="_blank" href="https://www.facebook.com/elansar.center"><i class="fa fa-facebook"
                                                                                 aria-hidden="true"></i></a></li>
        <li><a target="_blank" href="https://api.whatsapp.com/send?phone=%2B970599050702"><i class="fa fa-whatsapp"
                                                                                             aria-hidden="true"></i></a>
        </li>
        <li><a target="_blank" href="https://www.instagram.com/alansar_center_for_quran"><i class="fa fa-instagram"
                                                                                            aria-hidden="true"></i></a>
        </li>
        <li><a target="_blank" href="https://twitter.com/centeransarto"><i class="fa fa-twitter"
                                                                           aria-hidden="true"></i></a></li>
        <li><a target="_blank" href="https://t.me/centeransar1999"><i class="fa fa-telegram"
                                                                      aria-hidden="true"></i></a></li>
        <li><a target="_blank" href="https://www.tiktok.com/@centeransar1999"><img width="40" height="40"
                                                                                   src="{{ URL::asset('assets/images/tiktok.png',true) }}"
                                                                                   aria-hidden="true" alt="tiktok"/></a>
        </li>
    </ul>
</div>
@include('layouts.footer')
</body>
</html>
<style>
    body {
        font-family: 'Roboto', sans-serif;
    }

    .follow-widget {
        text-align: center;
        width: 90%;
        margin: 50px auto;
        display: table;
        border: 1px solid #ddd;
        padding: 10px 10px 20px;
        border-radius: 10px;
    }

    .follow-widget h2 {
        border-bottom: 1px solid #ddd;
        padding: 0px 0px 25px;
        font-size: 26px;
        letter-spacing: 1px;
    }

    .follow-widget ul {
        padding: 0px;
        margin: 0px;
    }

    .follow-widget li {
        float: left;
        list-style: none;
        font-size: 20px;
        text-align: center;
        margin: 10px 0px 10px;
        width: 16.666%;
    }

    .follow-widget li a i {
        background-color: #f1f1f1;
        color: #fff;
        border-radius: 100%;
        height: 40px;
        width: 40px;
        line-height: 43px !important;
    }

    .follow-widget li a i:hover {
        opacity: 0.8;
    }

    .follow-widget li a i.fa.fa-facebook {
        background-color: #4267B2;
    }

    .follow-widget li a i.fa.fa-twitter {
        background-color: #00acee;
    }

    .follow-widget li a i.fa.fa-telegram {
        background-color: #0088cc;
    }

    .follow-widget li a i.fa.fa-instagram {
        background-color: #e95950;
    }

    .follow-widget li a i.fa.fa-whatsapp {
        background-color: #25d366;
    }

    .follow-widget li a fa.fa-tiktok {
        background-color: #FF0000;
    }
</style>

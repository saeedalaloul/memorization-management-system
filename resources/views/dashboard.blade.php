<!DOCTYPE html>
<html lang="en">
@section('title')
    الرئيسية
@stop
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="HTML5 Template"/>
    <meta name="description" content="Webmin - Bootstrap 4 & Angular 5 Admin Dashboard Template"/>
    <meta name="author" content="potenzaglobalsolutions.com"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600&display=swap" rel="stylesheet">
    @include('layouts.head')
    @livewireStyles
</head>

<body style="font-family: 'Cairo', sans-serif">

<div class="wrapper" style="font-family: 'Cairo', sans-serif">

    <!--=================================
preloader -->

    <div id="pre-loader">
        <img src="{{ URL::asset('assets/images/pre-loader/loader-01.svg',true) }}" alt="">
    </div>

    <!--=================================
preloader -->

@include('layouts.main-header')

@include('layouts.main-sidebar')

<!--=================================
 Main content -->
    <!-- main-content -->
    <div class="content-wrapper">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="mb-0" style="font-family: 'Cairo', sans-serif">الرئيسية</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb pt-0 pr-0 float-left float-sm-right">
                    </ol>
                </div>
            </div>
        </div>
        <!-- widgets -->
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
                <div class="card card-statistics h-100">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                    <span class="text-success">
                                        <i class="fas fa-user-graduate highlight-icon" aria-hidden="true"></i>
                                    </span>
                            </div>
                            <div class="float-right text-right">
                                <p class="card-text text-dark">عدد الطلاب</p>
                                <h4>{{\App\Models\Student::count()}}</h4>
                            </div>
                        </div>
                        <p class="text-muted pt-3 mb-0 mt-2 border-top">
                            <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                                href="{{url('manage_student',null,true)}}" target="_blank"><span class="text-danger">عرض البيانات</span></a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
                <div class="card card-statistics h-100">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                    <span class="text-warning">
                                        <i class="fas fa-chalkboard-teacher highlight-icon" aria-hidden="true"></i>
                                    </span>
                            </div>
                            <div class="float-right text-right">
                                <p class="card-text text-dark">عدد المعلمين</p>
                                <h4>{{\App\Models\Teacher::count()}}</h4>
                            </div>
                        </div>
                        <p class="text-muted pt-3 mb-0 mt-2 border-top">
                            <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                                href="{{url('manage_teacher',null,true)}}" target="_blank"><span class="text-danger">عرض البيانات</span></a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
                <div class="card card-statistics h-100">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                    <span class="text-success">
                                        <i class="fas fa-user-tie highlight-icon" aria-hidden="true"></i>
                                    </span>
                            </div>
                            <div class="float-right text-right">
                                <p class="card-text text-dark">عدد اولياء الامور</p>
                                <h4>{{\App\Models\Father::count()}}</h4>
                            </div>
                        </div>
                        <p class="text-muted pt-3 mb-0 mt-2 border-top">
                            <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a href="#"
                                                                                        target="_blank"><span
                                    class="text-danger">عرض البيانات</span></a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
                <div class="card card-statistics h-100">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                    <span class="text-primary">
                                        <i class="fas fa-chalkboard highlight-icon" aria-hidden="true"></i>
                                    </span>
                            </div>
                            <div class="float-right text-right">
                                <p class="card-text text-dark">عدد حلقات التحفيظ</p>
                                <h4>{{\App\Models\Group::count()}}</h4>
                            </div>
                        </div>
                        <p class="text-muted pt-3 mb-0 mt-2 border-top">
                            <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                                href="{{url('manage_group',null,true)}}" target="_blank"><span class="text-danger">عرض البيانات</span></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Orders Status widgets-->


        <div class="row">

            <div style="height: 400px;" class="col-xl-12 mb-30">
                <div class="card card-statistics h-100">
                    <div class="card-body">
                        <div class="tab nav-border" style="position: relative;">
                            <div class="d-block d-md-flex justify-content-between">
                                <div class="d-block w-100">
                                    <h5 style="font-family: 'Cairo', sans-serif" class="card-title">اخر العمليات علي
                                        النظام</h5>
                                </div>
                                <div class="d-block d-md-flex nav-tabs-custom">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">

                                        <li class="nav-item">
                                            <a class="nav-link active show" id="students-tab" data-toggle="tab"
                                               href="#students" role="tab" aria-controls="students"
                                               aria-selected="true"> الطلاب</a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" id="teachers-tab" data-toggle="tab" href="#teachers"
                                               role="tab" aria-controls="teachers" aria-selected="false">المعلمين
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" id="parents-tab" data-toggle="tab" href="#parents"
                                               role="tab" aria-controls="parents" aria-selected="false">اولياء الامور
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" id="fee_exams-tab" data-toggle="tab"
                                               href="#fee_exams"
                                               role="tab" aria-controls="fee_exams" aria-selected="false">الإختبارات القرآنية
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                            <div class="tab-content" id="myTabContent">

                                {{--students Table--}}
                                <div class="tab-pane fade active show" id="students" role="tabpanel"
                                     aria-labelledby="students-tab">
                                    <div class="table-responsive mt-15">
                                        <table style="text-align: center"
                                               class="table center-aligned-table table-hover mb-0">
                                            <thead>
                                            <tr class="table-info text-danger">
                                                <th>#</th>
                                                <th>اسم الطالب</th>
                                                <th>البريد الالكتروني</th>
                                                <th>رقم الهوية</th>
                                                <th>اسم المرحلة</th>
                                                <th>اسم الحلقة</th>
                                                <th>اسم المحفظ</th>
                                                <th>تاريخ الاضافة</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse(\App\Models\Student::latest()->take(5)->get() as $student)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$student->user->name}}</td>
                                                    <td>{{$student->user->email}}</td>
                                                    <td>{{$student->user->identification_number}}</td>
                                                    <td>{{$student->grade->name}}</td>
                                                    <td>{{$student->group->name}}</td>
                                                    <td>{{$student->group->teacher->user->name}}</td>
                                                    <td class="text-success">{{$student->created_at}}</td>
                                                    @empty
                                                        <td class="alert-danger" colspan="8">لاتوجد بيانات</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{--teachers Table--}}
                                <div class="tab-pane fade" id="teachers" role="tabpanel" aria-labelledby="teachers-tab">
                                    <div class="table-responsive mt-15">
                                        <table style="text-align: center"
                                               class="table center-aligned-table table-hover mb-0">
                                            <thead>
                                            <tr class="table-info text-danger">
                                                <th>#</th>
                                                <th>اسم المحفظ</th>
                                                <th>رقم الهوية</th>
                                                <th>اسم المرحلة</th>
                                                <th>رقم الجوال</th>
                                                <th>تاريخ الاضافة</th>
                                            </tr>
                                            </thead>

                                            @forelse(\App\Models\Teacher::latest()->take(5)->get() as $teacher)
                                                <tbody>
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$teacher->user->name}}</td>
                                                    <td>{{$teacher->user->identification_number}}</td>
                                                    <td>{{$teacher->grade->name}}</td>
                                                    <td>{{$teacher->user->phone}}</td>
                                                    <td class="text-success">{{$teacher->created_at}}</td>
                                                    @empty
                                                        <td class="alert-danger" colspan="8">لاتوجد بيانات</td>
                                                </tr>
                                                </tbody>
                                            @endforelse
                                        </table>
                                    </div>
                                </div>

                                {{--parents Table--}}
                                <div class="tab-pane fade" id="parents" role="tabpanel" aria-labelledby="parents-tab">
                                    <div class="table-responsive mt-15">
                                        <table style="text-align: center"
                                               class="table center-aligned-table table-hover mb-0">
                                            <thead>
                                            <tr class="table-info text-danger">
                                                <th>#</th>
                                                <th>اسم ولي الأمر</th>
                                                <th>البريد الإلكتروني</th>
                                                <th>رقم الهوية</th>
                                                <th>رقم الهاتف</th>
                                                <th>تاريخ الإضافة</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse(\App\Models\Father::latest()->take(5)->get() as $father)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$father->user->name}}</td>
                                                    <td>{{$father->user->email}}</td>
                                                    <td>{{$father->user->identification_number}}</td>
                                                    <td>{{$father->user->phone}}</td>
                                                    <td class="text-success">{{$father->created_at}}</td>
                                                    @empty
                                                        <td class="alert-danger" colspan="8">لاتوجد بيانات</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{--sections Table--}}
                                <div class="tab-pane fade" id="fee_exams" role="tabpanel"
                                     aria-labelledby="fee_exams-tab">
                                    <div class="table-responsive mt-15">
                                        <table style="text-align: center"
                                               class="table center-aligned-table table-hover mb-0">
                                            <thead>
                                            <tr class="table-info text-danger">
                                                <th>#</th>
                                                <th>اسم الطالب</th>
                                                <th>جزء الإختبار</th>
                                                <th>درجة الإختبار</th>
                                                <th>اسم المحفظ</th>
                                                <th>اسم المختبر</th>
                                                <th>تاريخ الإختبار</th>
                                                <th>ملاحظات</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse(\App\Models\Exam::orderByRaw('exam_date')->limit(10)->get() as $exam)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ $exam->student->user->name }}</td>
                                                    <td>{{ $exam->QuranPart->name }}</td>
                                                    <td style="text-align: center; align-content: center">
                                                        @if ($exam->calcmarkexam() >= $exam->examSuccessMark->mark)
                                                            <div class="badge-success" style="width: 40px;">
                                                                {{ $exam->calcmarkexam().'%' }}
                                                            </div>
                                                        @else
                                                            <div class="badge-danger" style="width: 40px;">
                                                                {{ $exam->calcmarkexam().'%' }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>{{ $exam->teacher->user->name }}</td>
                                                    <td>{{ $exam->tester->user->name }}</td>
                                                    <td class="text-success">{{ $exam->exam_date }}</td>
                                                    <td>{{ $exam->notes }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="alert-danger" colspan="9">لاتوجد بيانات</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <livewire:calendar/>

        <!--=================================
wrapper -->

        <!--=================================
footer -->
        @include('layouts.footer')
    </div><!-- main content wrapper end-->
</div>
</div>
</div>

<!--=================================
footer -->

@include('layouts.footer-scripts')
@livewireScripts
@stack('scripts')
</body>

</html>

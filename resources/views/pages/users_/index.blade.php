@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    المستخدمين
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    المستخدمين
@stop
<!-- breadcrumb -->
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            @can('اضافة مستخدم')
                <div class="pull-right">
                    <a class="btn btn-success" href="{{ route('users.create') }}">اضافة مستخدم جديد</a>
                </div><br><br><br>
            @endcan
        </div>
    </div>


    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif


    <table class="table table-bordered">
        <tr>
            <th>#</th>
            <th>الإسم</th>
            <th>البريد الإلكتروني</th>
            <th>الأدوار</th>
            <th>العمليات</th>
        </tr>
        @foreach ($data as $key => $user)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if(!empty($user->getRoleNames()))
                        @foreach($user->getRoleNames() as $v)
                            <label class="badge badge-success">{{ $v }}</label>
                        @endforeach
                    @endif
                </td>
                <td>
                    @can('عرض مستخدم')
                        <a class="btn btn-info" href="{{ route('users.show',$user->id) }}">عرض</a>
                    @endcan
                    @can('تعديل مستخدم')
                        <a class="btn btn-primary" href="{{ route('users.edit',$user->id) }}">تعديل</a>
                    @endcan
                    @can('حذف مستخدم')
                        {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
                        {!! Form::submit('حذف', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>


    {!! $data->render() !!}
@endsection
@section('js')
    @toastr_js
    @toastr_render
@endsection

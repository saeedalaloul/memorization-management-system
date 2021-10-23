@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    أدوار المستخدمين
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    أدوار المستخدمين
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-right">
                @can('اضافة دور')
                    <a class="btn btn-success" href="{{ route('roles.create') }}"> اضافة دور جديد</a>
                    <br><br><br>
                @endcan
            </div>
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
            <th>اسم الدور</th>
            <th>العمليات</th>
        </tr>
        @foreach ($roles as $key => $role)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $role->name }}</td>
                <td>
                    <a class="btn btn-info" href="{{ route('roles.show',$role->id) }}">عرض</a>
                    @can('تعديل دور')
                        <a class="btn btn-primary" href="{{ route('roles.edit',$role->id) }}">تعديل</a>
                    @endcan
                    @can('حذف دور')
                        {!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline']) !!}
                        {!! Form::submit('حذف', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>


    {!! $roles->render() !!}
@endsection
@section('js')
    @toastr_js
    @toastr_render
@endsection

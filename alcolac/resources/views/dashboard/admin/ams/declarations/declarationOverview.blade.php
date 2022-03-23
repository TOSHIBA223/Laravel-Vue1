@extends('dashboard.base')

@section('content')
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="/js/tablefilter/tablefilter.js"></script>
    <script src="/js/declarationSection.js"></script>
    <div class="container-fluid ams-wrapper">
        <div class="animated fadeIn">
            <a href="/admin/declarations/generate-csv" class="btn btn-success mb-3">
                Download a CSV Report
            </a>
            <div class="row">
                <div class="col-sm-12 col-md-6" id="user-list">
                    <div class="card">
                        <div class="card-header">
                            <div class="header-text-wrapper">
                                <i class="fa fa-align-justify"></i>{{ __('User Declarations') }}
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-striped filtered-table filtered-table-users">
                                <thead>
                                <tr>
                                    <th>Employee Code</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                            <td>
                                                <a href="#" data-user-id="{{$user->id}}">
                                                {{ $user->employee_code }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" data-user-id="{{$user->id}}">
                                                {{ $user->first_name }} {{ $user->last_name }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" data-user-id="{{$user->id}}">
                                                {{ $user->phone }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" data-user-id="{{$user->id}}">
                                                {{ $user->email }}
                                                </a>
                                            </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6" id="user-info">
                    <div class="card">
                        <div class="card-header">
                            <div class="header-text-wrapper">
                                <span class="user-selected">
                                    Select a user from the table
                                </span>
                            </div>
                        </div>
                        <div class="card-body add-declaration-item">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/declarationFilterUsers.js"></script>
@endsection


@section('javascript')

@endsection


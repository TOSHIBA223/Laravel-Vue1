@extends('dashboard.base')

@section('content')
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <div class="container-fluid ams-wrapper">
        <div class="animated fadeIn">
            <a href="/admin/declarations" class="btn btn-success mb-3">
                Return
            </a>
            <div class="row">
                <div class="col-12" id="user-list">
                    <div class="card">
                        <div class="card-header">
                            <div class="header-text-wrapper">
                                <i class="fa fa-align-justify"></i>{{ __('User Declarations') }}
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="/admin/declarations/export" method="GET">
                            <table class="table table-responsive-sm table-striped">
                                <thead class="text">
                                <tr>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="date" id="start-date" name="date_start">
                                        </td>
                                        <td>
                                            <input type="date" id="end-date" name="date_end">
                                        </td>
                                        <td class="text-center">
                                            <button type="submit" class="btn btn-primary mt-0">Generate CSV</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


@section('javascript')

@endsection


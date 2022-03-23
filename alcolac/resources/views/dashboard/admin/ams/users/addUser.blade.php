@extends('dashboard.base')

@section('content')
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="/js/sendQuestionnaire.js"></script>
    <script src="/js/tablefilter/tablefilter.js"></script>
    <div class="container-fluid ams-wrapper">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="header-text-wrapper">
                                <i class="fa fa-align-justify"></i>{{ __('Users') }}
                            </div>
                            <a href="/admin/ams-users/add-user" class="btn btn-success btn-user-table">
                                Add User
                            </a>
                        </div>
                        <div class="card-body">
                            <form action="/admin/ams-users/add-user/create">

                                <div class="container">
                                    <div class="row mb-4">
                                        <div class="input-wrapper col-md-4">
                                            <input type="text"
                                                    name="first_name"
                                                   id="first-name"
                                                   placeholder="First Name*"
                                                   required>
                                        </div>
                                        <div class="input-wrapper col-md-4">
                                            <input type="text"
                                                    name="last_name"
                                                   id="last-name"
                                                   placeholder="Last Name*"
                                                   required>
                                        </div>
                                        <div class="input-wrapper col-md-4">
                                            <input type="text"
                                                   name="employee_code"
                                                   id="employee_code"
                                                   placeholder="Employee Code*"
                                                   required>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="input-wrapper col-md-4">
                                            <input type="email"
                                                   name="email"
                                                   id="email"
                                                   placeholder="Email">
                                        </div>
                                        <div class="input-wrapper col-md-4">
                                            <input type="text"
                                                   name="phone"
                                                   id="phone"
                                                   placeholder="phone (must start with +614)*"
                                                   required>
                                        </div>
                                        <div class="input-wrapper col-md-4">
                                            <input text="text"
                                                   name="groups"
                                                   id="group"
                                                   placeholder="Department*
                                                    required">
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <h3 class="ams-header col-12">
                                            Address
                                        </h3>
                                        <div class="column-left col-md-6">
                                            <input type="text"
                                                   name="address"
                                                   id="address"
                                                   placeholder="Address*"
                                                   required>
                                        </div>
                                        <div class="column-right col-md-6">
                                            <input type="text"
                                                   name="suburb"
                                                   id="suburb"
                                                   placeholder="Suburb*"
                                                   required>
                                        </div>
                                        <div class="col-12 mb-4"></div>
                                        <div class="column-left col-md-6">
                                            <select name="state"
                                                   id="address"
                                                    required>
                                                <option disabled selected>Select Suburb</option>
                                                <option value="ACT">ACT</option>
                                                <option value="NSW">NSW</option>
                                                <option value="NT">NT</option>
                                                <option value="QLD">QLD</option>
                                                <option value="SA">SA</option>
                                                <option value="Tas">Tas</option>
                                                <option value="Vic">Vic</option>
                                                <option value="WA">WA</option>
                                            </select>
                                        </div>
                                        <div class="column-right col-md-6">
                                            <input type="text"
                                                   name="post_code"
                                                   id="post-code"
                                                   placeholder="Post Code*">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="input-wrapper col-md-6">
                                            <select name="menuroles"
                                                    id="role">
                                                <option disabled selected>Select Role</option>
                                                @foreach( $roles as $role)
                                                    <option value="{{$role->name}}}}">{{$role->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="input-wrapper col-md-6">
                                            <input type="password"
                                                name="password"
                                                    id="role"
                                                    placeholder="Password*">
                                        </div>
                                    </div>
                                    <div class="row flex-row-reverse">
                                        <button class="btn btn-primary"
                                                type="submit">Add User</button>
                                    </div>
                                </div>
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


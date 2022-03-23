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
                            <a href="/admin/ams-users/add" class="btn btn-success btn-user-table">
                                Add User
                            </a>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-striped filtered-table">
                                <thead>
                                <tr>
                                    <th>Employee Code</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Department</th>
                                    <th>Address</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->employee_code }}</td>
                                        <td>{{ $user->first_name }}</td>
                                        <td>{{ $user->last_name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->groups }}</td>
                                        <td>{{ $user->address . ', ' . $user->suburb . ', ' . $user->state . ' ' . $user->post_code }}</td>
                                        <td class="edit-items">
                                            <a href="/admin/ams-users/<?=$user->id?>" class="text-primary">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="/admin/ams-users/<?=$user->id?>/edit" class="text-primary">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            @if( $you->id !== $user->id )
                                                <form class="delete-user" action="/admin/ams-users/delete/<?=$user->id?>" method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button class="none">
                                                        <i class="fa fa-ban"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <div action="{{route('createBlank') }}" method="POST" class="declaration-send-wrapper">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{$user->id}}">
                                                <input type="hidden" name="questionnaire_id" value="1">
                                                <button id="create-blank-questionnaire" class="btn btn-block btn-success btn-user-table" style="margin-top: 10px;">Send Declaration</button>
                                                <button id="create-blank-questionnaire-preverified"
                                                        class="btn btn-block btn-success btn-user-table"
                                                        value="pre-verified"
                                                        name="pre-verified"
                                                        type="submit"
                                                        style="margin-top: 10px;">Send Pre-Verified</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/tableFilter-init.js"></script>
@endsection


@section('javascript')

@endsection


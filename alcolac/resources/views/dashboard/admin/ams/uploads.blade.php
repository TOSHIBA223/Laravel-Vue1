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
                                <i class="fa fa-align-justify"></i>{{ __('Files') }}
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-striped filtered-table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Path</th>
                                    <th>Link</th>
                                    <th>Status</th>
                                    <th>Views</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($uploads as $file)
                                    <tr>
                                        <td>{{ $file->id }}</td>
                                        <td>{{ $file->name }}</td>
                                        <td>storage/app/{{ $file->path }}</td>
                                        <td>@php echo URL::to('/') @endphp/file/{{ $file->key }}</td>
                                        <td><?= $file->archived === 1 ? 'Archived' : 'Open' ?></td>
                                        <td>{{ $file->views }}</td>
                                        <td>
                                            <form action="/admin/files/delete/<?=$file->id?>" method="POST">
                                                <button type="submit" class="file-action">
                                                    <i class="fa fa-trash text-danger"></i>
                                                </button>
                                            </form>
                                            @if($file->archived === 0)
                                            <form action="/admin/files/archive/<?=$file->id?>" method="POST">
                                                <button type="submit" class="file-action">
                                                    <i class="fa fa-archive text-warning"></i>
                                                </button>
                                            </form>
                                            @else
                                            <form action="/admin/files/open/<?=$file->id?>" method="POST">
                                                <button type="submit" class="file-action">
                                                    <i class="fa fa-check text-success"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @if($upload === 'fail')
                <p class="error text-center text-danger">Upload Failed. Please try again.</p>
            @endif
            @if($upload === 'success')
                <p class="error text-center text-success">Upload Successful.</p>
            @endif
            <form action="/admin/files/upload" method="POST" enctype="multipart/form-data">
                <h3>Upload a file</h3>
                <input type="file" name="upload">
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
    <script src="/js/tableFilter-init.js"></script>
    <style>
        .file-action {
            border: 0;
            padding: 0;
            margin-right: 10px;
            font-size: 20px;
        }
        .file-action:focus {
            border: 0;
            outline: 0;
        }

        .card-body form {
            display: inline-block;
        }
    </style>
@endsection


@section('javascript')

@endsection


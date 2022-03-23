@extends('dashboard.base')

@section('content')
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="/js/sendPoll.js"></script>
    <script src="/js/tablefilter/tablefilter.js"></script>
    <script src="/js/tableFilter-init.js"></script>
        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i>{{ __('Polls') }}</div>
                    <div class="card-body">
                        <table class="table table-responsive-sm table-striped">
                        <thead>
                          <tr>
                            <th>Complete/Total</th>
                            <th>23rd of October (Current)</th>
                            <th>26th of October (New)</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                              <td>
                                  {{$complete}}/{{$total}}
                              </td>
                              <td>
                                  {{$first}}
                              </td>
                              <td>
                                  {{$second}}
                              </td>
                          </tr>
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


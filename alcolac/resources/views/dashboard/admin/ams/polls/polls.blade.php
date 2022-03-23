@extends('dashboard.base')

@section('content')
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
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
                        <table class="table table-responsive-sm table-striped filtered-table">
                        <thead>
                          <tr>
                            <th>Name</th>
                            <th></th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($polls as $poll)
                            <tr>
                              <td>
                                  {{ $poll->name }}
                              </td>
                                <td>
                                    <a href="{{route('showPollData', $poll->id) }}"
                                       class="btn btn-block btn-danger">View Poll</a>
                                </td>
                              <td>
                                <a href="#" data-id="{{$poll->id}}"
                                   class="btn btn-block btn-primary open-send">Send Poll</a>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                    <div class="overlay">
                        <div class="overlay-container">
                            <a href="#" class="close-overlay">
                                <i class="fa fa-times"></i>
                            </a>
                            <form method="POST" action="{{route('sendPoll')}}">
                                @csrf
                                <input type="hidden" name="poll_id" value="1">
                                <label for="location">
                                    Location
                                    <select id="location" name="location" class="select2 single">
                                        <option value="all" selected>All</option>
                                        @foreach($locations as $location)
                                            <option value="{{$location->location}}">{{$location->location}}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <label for="groups">
                                    Groups
                                    <select id="groups" name="groups[]" class="select2 multiple" multiple="multiple">
                                        <option value="all" selected>All</option>
                                        @foreach($groups as $group)
                                            <option value="{{$group->groups}}">{{$group->groups}}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <button type="submit"
                                        id="send-poll"
                                        class="btn btn-block btn-danger">Send Poll</button>
                            </form>
                        </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    <script src="/js/tableFilter-init.js"></script>
    <script>
        jQuery(document).ready(function($) {
            $('.select2.single').select2();

            $('.select2.multiple').select2({
                closeOnSelect: false
            });

            $('body').on( 'click', '.open-send', function(e) {
                e.preventDefault();

                $('.overlay').addClass('open');
            });

            $('body').on( 'click', '.close-overlay', function(e) {
                e.preventDefault();

                $('.overlay').removeClass('open');
            });
        });
    </script>

@endsection


@section('javascript')

@endsection


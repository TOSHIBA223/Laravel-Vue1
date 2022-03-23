@extends('layouts.frontend')

@section('content')
    <script src="/js/add-code.js"></script>
    <div class="container declaration-wrapper">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card-group">
                    <div class="card text-white py-5 logo d-md-none mobile-logo" style="width:44%; background: ;">
                        <div class="card-body text-center" style="display: flex; align-items: center; justify-content: center;">
                            <div>
                                <img src="/images/LambCo.png">
                            </div>
                        </div>
                    </div>
                    <div class="card p-4">
                        <div class="card-body">
                            @if(isset($_GET['code']) && $_GET['code'] === 'sent')
                                <p class="text-success text-center mb-3 font-weight-bold">
                                    A new code is on it's way
                                </p>
                            @endif
                            <h1>Declaration System</h1>
                            <p class="text-muted text-center" style="font-size: 16px;">Add your unique code below to access your declaration</p>
                            <form method="GET" action="">
                                <div class="input-group mb-3" style="max-width: 250px; margin-left: auto; margin-right: auto;">
                                    <input class="form-control" type="text" placeholder="{{ __('Code') }}" name="token" value="" required autofocus>
                                </div>
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <button class="btn btn-primary px-4" type="submit">{{ __('Start Now') }}</button>
                                    </div>
                                    <div class="col-12 text-center">
                                        <p class="new-code mt-3">
                                            Can't find your declaration code?
                                            <a href="/send-new-code" class="send-new">
                                                Send me a new code
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card text-white py-5 logo d-md-down-none" style="width:44%; background: ;">
                        <div class="card-body text-center" style="display: flex; align-items: center; justify-content: center;">
                            <div>
                                <img src="/images/LambCo.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')

@endsection

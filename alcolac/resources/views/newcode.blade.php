@extends('layouts.frontend')

@section('content')

    <script src="/js/selfSend.js"></script>
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
                            <h1>Declaration System - New Code</h1>
                            <p class="text-muted text-center" style="font-size: 16px;">Enter your mobile number and Birth Date to send yourself a new code</p>
                            <div class="ajax-form" style="max-width: 250px; width: 100%;">
                                @csrf
                                <div class="input-group mb-3">
                                    <input class="form-control" type="text" placeholder="{{ __('Phone') }}" name="phone" value="" required autofocus>
                                </div>
                                <div class="input-group mb-3 text-center" style="display: flex; align-items: center; justify-content: center;">
                                    <select id="dob-day"
                                            placeholder="Date of Birth"
                                            name="dob-day"
                                    style="margin-right: 3px;">
                                        <option>DD</option>
                                        <?php
                                        for($i = 1; $i <= 31; $i++) {
                                            echo "<option value='".str_pad($i, 2, '0', STR_PAD_LEFT)."'>$i</option>";
                                        }
                                        ?>
                                    </select>

                                    <select id="dob-month"
                                            placeholder="Date of Birth"
                                            name="dob-month"
                                            style="margin-right: 3px;">
                                        <option>MM</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">Novemeber</option>
                                        <option value="12">December</option>
                                    </select>

                                    <select id="dob-year"
                                            placeholder="Date of Birth"
                                            name="dob-year">
                                        <option>YYYY</option>
                                        <?php
                                        $date = date('Y', strtotime('today -15 years'));
                                        for($i = $date; $i >= 1920; $i--) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <button class="btn btn-primary px-4" type="submit" id="submit">{{ __('Send New Code') }}</button>
                                    </div>
                                </div>
                            </div>
                            <p class="send-error text-danger mt-2" style="display: none;"></p>
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

<!DOCTYPE html>
<!--
* CoreUI - Free Bootstrap Admin Template
* @version v3.0.0-alpha.1
* @link https://coreui.io
* Copyright (c) 2019 creativeLabs Łukasz Holeczek
* Licensed under MIT (https://coreui.io/license)
-->

<html lang="en">
  <head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="ALC - AMS">
    <title>ALC - AMS</title>
    <link rel="apple-touch-icon" sizes="57x57" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="60x60" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="72x72" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="76x76" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="114x114" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="120x120" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="144x144" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="152x152" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon.ico">
    <link rel="icon" sizes="192x192" href="/favicon.ico">
    <link rel="icon" sizes="32x32" href="/favicon.ico">
    <link rel="icon" sizes="96x96" href="/favicon.ico">
    <link rel="icon" sizes="16x16" href="/favicon.ico">
    <link rel="manifest" href="assets/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="assets/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Icons-->
    <link href="{{ asset('css/free.min.css') }}" rel="stylesheet"> <!-- icons -->
    <link href="{{ asset('css/flag-icon.min.css') }}" rel="stylesheet"> <!-- icons -->
    <!-- Main styles for this application-->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <!-- Global site tag (gtag.js) - Google Analytics-->



    <link href="{{ asset('css/coreui-chartjs.css') }}" rel="stylesheet">
      <link href="https://ams.alcolac.com.au/css/style.css" rel="stylesheet">

  </head>
  <body class="c-app flex-row align-items-center">

    @yield('content')

    <!-- CoreUI and necessary plugins-->
    <script src="{{ asset('js/coreui.bundle.min.js') }}"></script>

    @yield('javascript')

  </body>
</html>

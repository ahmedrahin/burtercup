<!DOCTYPE html>
<!--[if IE 8 ]><html class="ie" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta name="author" content="themesflat.com">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>@yield('title') || {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset($system_settings->favicon) }}">

    @include('backend.partials.style')

</head>

<body class="body">

    <div id="wrapper">
        <!-- #page -->
        <div id="page" class="">
            <!-- layout-wrap -->
           <div class="layout-wrap">
                <!-- preload -->
                <div id="preload" class="preload-container">
                    <div class="preloading">
                        <span></span>
                    </div>
                </div>

                @include('backend.partials.sidebar')

                <div class="section-content-right">
                    @include('backend.partials.header')
                    <div class="main-content">
                        <!-- main-content-wrap -->
                        <div class="main-content-inner">
                            @yield('content')
                        </div>

                        @include('backend.partials.footer')

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('backend.partials.scripts')

</body>

</html>

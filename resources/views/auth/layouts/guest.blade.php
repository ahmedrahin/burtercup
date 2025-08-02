<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/animate.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/animation.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/font/fonts.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/icon/style.css') }}">
        <link rel="stylesheet" type="text/css" href="{{asset('assets/css/toastify.css')}}"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    </head>
    <body class="font-sans text-gray-900 antialiased">

         {{ $slot }}

    </body>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    {{-- messages --}}
    <script src="{{asset('assets/js/toastify.js')}}"></script>
    @if (session('success') || session('error') || session('info') || session('warning'))
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let message = "{{ session('success') ?? session('error') ?? session('info') ?? session('warning') }}";
            let type = "{{ session('success') ? 'success' : (session('error') ? 'error' : (session('info') ? 'info' : 'warning')) }}";

            let iconHtml = "";
            switch (type) {
                case "success":
                    iconHtml = '<i class="bi bi-check-circle-fill" style="color: #28a745; font-size: 18px;"></i>';
                    break;
                case "error":
                    iconHtml = '<i class="bi bi-x-circle-fill" style="color: #e74c3c; font-size: 18px;"></i>';
                    break;
                case "info":
                    iconHtml = '<i class="bi bi-info-circle-fill" style="color: #3498db; font-size: 18px;"></i>';
                    break;
                case "warning":
                    iconHtml = '<i class="bi bi-exclamation-circle-fill" style="color: #f39c12; font-size: 18px;"></i>';
                    break;
                default:
                    iconHtml = "";
            }

            Toastify({
                text: `<span>
                        <span style="margin-right: 6px; font-size: 18px;">${iconHtml}</span>
                        <span>${message}</span>
                    </span>`,
                duration: 3000,
                gravity: "bottom",
                position: "left",
                close: true,
                escapeMarkup: false,
                style: {
                    background: "#fff",
                    color: "#000",
                    boxShadow: "0px 4px 10px rgba(0,0,0,0.1)",
                    borderRadius: "8px",
                    padding: "20px 15px",
                    fontSize: "16px",
                    fontWeight: '600',
                   
                    border: "2px solid #ddd",
                    marginBottom: "5px",
                    bottom: "100px",
                    marginBottom: "30px",
                }
            }).showToast();
        });
    </script>
@endif
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="{{asset('manifest.json')}}" />
    <link rel="icon" type="image/x-icon"
        href="{{ asset('resources/assets/img/logo_baru_mozaic/logo baru set-09.png') }}" />

    {{-- Custom Meta Tags --}}
    @yield('meta_tags')

    {{-- Title --}}
    <title>
        @yield('title_prefix', config('adminlte.title_prefix', ''))
        @yield('title', config('adminlte.title', 'KAROTA KING'))
        @yield('title_postfix', config('adminlte.title_postfix', ''))
    </title>

    {{-- Custom stylesheets (pre AdminLTE) --}}
    @yield('adminlte_css_pre')

    {{-- Base Stylesheets --}}
    @if (!config('adminlte.enabled_laravel_mix'))
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">


        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" />
        <link rel="stylesheet" href="{{ asset('resources/css/app.css') }}">
        {{-- <link rel="stylesheet" href="{{ asset('resources/css/select2.min.css') }}"> --}}
        
        {{-- Configured Stylesheets --}}
        @include('adminlte::plugins', ['type' => 'css'])
        
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
        <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <link rel="stylesheet" href="{{ asset('resources/css/colors.min.css') }}">
    @else
        <link rel="stylesheet" href="{{ mix(config('adminlte.laravel_mix_css_path', 'css/app.css')) }}">
    @endif

    {{-- Livewire Styles --}}
    @if (config('adminlte.livewire'))
        @if (app()->version() >= 7)
            @livewireStyles
        @else
            <livewire:styles />
        @endif
    @endif

    {{-- Custom Stylesheets (post AdminLTE) --}}
    @yield('adminlte_css')

    {{-- Favicon --}}
    @if (config('adminlte.use_ico_only'))
        <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
    @elseif(config('adminlte.use_full_favicon'))
        <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicons/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicons/apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicons/apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicons/apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicons/apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicons/apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicons/apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicons/apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicons/apple-icon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicons/favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicons/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicons/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicons/android-icon-192x192.png') }}">
        <link rel="manifest" href="{{ asset('favicons/manifest.json') }}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">
    @endif

</head>
<style>
    .pull-left {
        float: left !important;
    }

    .content-wrapper {
        overflow-x: hidden;
    }

    .loading {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #e05c03;
        border-right: 16px solid #d8b407;
        border-bottom: 16px solid #e05c03;
        border-left: 16px solid #d8b407;
        width: 120px;
        height: 120px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
    }

    .loading-widget {
        position: fixed;
        z-index: 50;
        width: 60px;
        height: 60px;
        top: 5em;
        right: 40px;
        text-align: center;
        display: none;
    }

    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
    .select2-container--disabled{
        background-color: #bcbcbc;
    }
</style>

<body class="@yield('classes_body')" @yield('body_data')>
    <div id="loading-widget" class="loading loading-widget mx-auto">
    </div>
    {{-- Body Content --}}
    @yield('body')

    {{-- Base Scripts --}}
    @if (!config('adminlte.enabled_laravel_mix'))
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
        <script src="{{ asset('vendor/moment/moment.min.js') }}"></script>

        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        {{-- <script src="{{ asset('resources/js/select2.min.js') }}"></script> --}}
        <script>
            /**
             * Show loading modal
             * @param  {Number} status Use 0 to close modal
             * @return {Void}   Nothing
             */
            function loading(status = 1) {
                if (status) {
                    $('#loading').modal('show');
                } else {
                    $('#loading').modal('hide');
                    $('.modal-backdrop .fade').hide();
                    setTimeout(() => {
                        $('.modal-backdrop .fade').hide();
                    }, 1000);
                }
            }
            /**
             * Show loading Widget
             * @param  {Number} status Use 0 to hida loading
             * @return {Void}   Nothing
             */
            function loadingWidget(status = 1) {
                if (status) {
                    $('#loading-widget').show();
                } else {
                    $('#loading-widget').hide();
                }
            }

            function toRp(number = 0) {
                var number = number.toString(),
                    rupiah = number.split(',')[0],
                    cents = (number.split(',')[1] || '') + '00';
                rupiah = rupiah.split('').reverse().join('')
                    .replace(/(\d{3}(?!$))/g, '$1,')
                    .split('').reverse().join('');
                return rupiah + ',' + cents.slice(0, 2);
            }
            $(document).ready(function() {
                $('#example').dataTable({
                    "aLengthMenu": [
                        [5, 15, 20, -1],
                        [5, 15, 20, "All"] // change per page values here
                    ],
                    // // set the initial value
                    "iDisplayLength": 5,
                    // "oLanguage": {
                    //     "sLengthMenu": "_MENU_ records",
                    //     "oPaginate": {
                    //         "sPrevious": "Prev",
                    //         "sNext": "Next"
                    //     }
                    // },
                    // "aoColumnDefs": [
                    //     { 'bSortable': false, 'aTargets': [0] },
                    //     { "bSearchable": false, "aTargets": [ 0 ] }
                    // ]
                });
                $('#example').addClass('pull-left');
            });
            $(document).ready(function() {
                if($('#merchant_id_view').val()!=''){
                     $('#merchant_id').val($('#merchant_id_view').val());
                 }
                $("[data-toggle=popover]").popover({
                    trigger: 'focus',
                    content: '<div class="spinner-border spinner-border-sm text-secondary" role="status"><span class="sr-only">Loading...</span></div>',
                });
                $("[data-toggle=popover]").on("shown.bs.popover", function () {
                    $.ajax({
                        url: "{{ route('quote') }}",
                        type: "GET",
                        success: function (result) {
                            $(".popover-body").html(result);
                            $("[data-toggle=popover]").popover("update");
                        },
                        error: function (data) {
                            console.log(data);
                            $(".popover-body").html("<em class='text-danger'>Error ... </em>");
                            $("[data-toggle=popover]").popover("update");
                        }
                    });
                });

                $('.selection-search-clear').select2({
                    theme: "bootstrap",
                    placeholder: "Select",
                    allowClear: true,
                    width: 'resolve',
                });
                $('.datatables').dataTable({
                        "aLengthMenu": [
                            [5, 15, 20, -1],
                            [5, 15, 20, "All"]
                        ],
                        "iDisplayLength": 5,
                    });
                $(".datatables").addClass('pull-left'); 
            });
            // $('.date').datepicker({ dateFormat: 'dd-mm-yy' }).val();
        </script>

        {{-- Configured Scripts --}}
        @include('adminlte::plugins', ['type' => 'js'])

        <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @else
        <script src="{{ mix(config('adminlte.laravel_mix_js_path', 'js/app.js')) }}"></script>
    @endif

    {{-- Livewire Script --}}
    @if (config('adminlte.livewire'))
        @if (app()->version() >= 7)
            @livewireScripts
        @else
            <livewire:scripts />
        @endif
    @endif

    {{-- Custom Scripts --}}
    @yield('adminlte_js')

</body>

</html>

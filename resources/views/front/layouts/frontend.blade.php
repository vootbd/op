<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title -->
    <title>離島データベース @yield('title')</title>

    <!-- Meta data -->
    <meta name="author" content="Rito DB" />
    <meta name="description" content="@yield('seo_description')"/>
    <meta name="Resource-type" content="@yield('seo_resource_type')" />
    <meta name="keywords" content="@yield('seo_keywords')">
    <link rel="image_src" href="@yield('seo_image')"/>

    @include('admin.partials.favicon')

     <!-- Styles -->
     <link href="https://fonts.googleapis.com/css?family=Noto+Sans+JP:300,400,500,700&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}">
     {{-- <link rel="stylesheet" href="{{ asset('css/admin/css/fonts.css') }}"> --}}
     <link rel="stylesheet" href="{{ asset('vendor/bootstrap_v4.3.1/css/bootstrap.min.css') }}">
     <link rel="stylesheet" href="{{ asset('vendor/rito-icon/rito-icon.css') }}">
     <link rel="stylesheet" href="{{ asset('vendor/swiper/dist/css/swiper.css') }}">
     <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}" />
     <!-- Theme CSS -->
     <link rel="stylesheet" href="{{ asset('css/admin/css/style.css') }}">
     <link rel="stylesheet" href="{{asset('css/admin/css/navbar.css')}}">
     <link rel="stylesheet" href="{{asset('css/admin/css/breadcrumb.css')}}">
     <link rel="stylesheet" href="{{asset('css/admin/css/sidebar.css')}}">
     <link rel="stylesheet" href="{{asset('css/admin/css/footer.css')}}">
     <link rel="stylesheet" href="{{asset('css/admin/css/admin.css')}}">
    
     @stack('custom-style')
     
     <!--[if lt IE 9]>
         <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
         <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
         <link rel='stylesheet' href="css/ie/ie8.css">
     <![endif]-->

</head>
<body>
    <div id="page-container">
        @include('front.partials.navbar')

        @include('admin.partials.breadcrumb')

        <div class="main-content">
            <div class="container-fluid d-flex">
                @yield('content')
            </div>
        </div>

        @include('admin.partials.footer')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery_v3.3.1/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap_v4.3.1/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/tmpl.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>
    <!-- Common Scripts -->
    <script>
        var SITEURL = "{{ URL::to('') }}";
    
        $( document ).ready( function () {
        $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });
        });
    </script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/fileupload.js') }}"></script>
    @stack('custom-scripts')

    @include('admin.partials.analytics')

</body>
</html>
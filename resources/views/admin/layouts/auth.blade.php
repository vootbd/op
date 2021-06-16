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
     {{-- <link rel="stylesheet" href="{{ asset('css/admin/css/fonts.css') }}"> --}}
     <link rel="stylesheet" href="{{ asset('vendor/bootstrap_v4.3.1/css/bootstrap.min.css') }}">
     <link rel="stylesheet" href="{{ asset('vendor/rito-icon/rito-icon.css') }}">
     
     <!-- Theme CSS -->
     <link rel="stylesheet" href="{{asset('css/admin/css/auth.css')}}">
     <link rel="stylesheet" href="{{asset('css/admin/css/footer-auth.css')}}">
    
     @stack('custom-style')
     
     <!--[if lt IE 9]>
         <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
         <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
         <link rel='stylesheet' href="css/ie/ie8.css">
     <![endif]-->

</head>
<body>
    <div class="d-flex justify-content-center w-100 main-content">
        @yield('content')
        @include('admin.partials.footer')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery_v3.3.1/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap_v4.3.1/js/bootstrap.min.js') }}"></script>

    <!-- Common Scripts -->
    {{-- <script src="{{ asset('web/js/auth.js') }}"></script> --}}

    <script>
        $(document).ready(function() {
            // alert close
            $("#alertClose").on("click", function(){
                $(".custom-alert").fadeOut("slow");
            });
            setTimeout(function() {
                $(".custom-alert").fadeOut("slow");
            }, 10000);
        });

        // field required text change for submit & hover 
        var elements = document.getElementsByTagName( "input" );
        for ( var i = 0; i < elements.length; i++ ) {
            elements[ i ].oninvalid = function ( e ) {
                e.target.setCustomValidity( "" );
                if ( !e.target.validity.valid ) {
                    switch ( e.target.id ) {
                        // case 'email':
                        //     e.target.setCustomValidity( "無効な電子メールアドレス" ); break;
                        default: e.target.setCustomValidity( "この項目は必須です。" ); break;
                    }
                }
            };
            elements[ i ].oninput = function ( e ) {
                e.target.setCustomValidity( "" );
            };
        }

        //Text input title remove text
        $( 'input' ).attr( 'title', '' );
    </script>
    
    @stack('custom-scripts')
    
    @include('admin.partials.analytics')

</body>
</html>
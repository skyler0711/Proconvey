<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \Laravel\Nova\Nova::rtlEnabled() ? 'rtl' : 'ltr' }}" class="h-full font-sans antialiased">
<head>
    <meta name="theme-color" content="#fff">
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width"/>
    <meta name="locale" content="{{ str_replace('_', '-', app()->getLocale()) }}"/>

    @include('nova::partials.meta')

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('app.css', 'vendor/nova') }}">

    <style>
        @font-face {
            font-family: GT Eesti Pro;
            font-weight: 400;
            src: url({{ asset('/fonts/GTEestiProDisplay-Regular.woff2') }}) format('woff2'),
                url({{ asset('/fonts/GTEestiProDisplay-Regular.woff') }}) format('woff2');
        }
        
        @font-face {
            font-family: GT Eesti Pro;
            font-weight: 500;
            src: url({{ asset('/fonts/GTEestiProDisplay-Medium.woff2') }}) format('woff2'),
                url({{ asset('/fonts/GTEestiProDisplay-Medium.woff') }}) format('woff2');
        }

        @font-face {
            font-family: GT Eesti Pro;
            font-weight: 700;
            src: url({{ asset('/fonts/GTEestiProDisplay-Bold.woff2') }}) format('woff2'),
                url({{ asset('/fonts/GTEestiProDisplay-Bold.woff') }}) format('woff2');
        }

        .font-sans {
            font-family: 'GT Eesti Pro', sans-serif;
        }

        .sidebar-item:focus, .sidebar-item:hover, .sidebar-section-title:hover {
            background-color: rgba(var(--colors-primary-200));
        }
    </style>

    @if ($styles = \Laravel\Nova\Nova::availableStyles(request()))
    <!-- Tool Styles -->
        @foreach($styles as $asset)
            <link rel="stylesheet" href="{!! $asset->url() !!}">
        @endforeach
    @endif
</head>
<body class="min-h-full text-sm font-medium text-gray-500 min-w-site dark:text-gray-400 bg-primary-100 dark:bg-primary-900">
    @inertia
    <div class="relative z-50">
      <div id="notifications" name="notifications"></div>
    </div>
    <div>
      <div id="dropdowns" name="dropdowns"></div>
      <div id="modals" name="modals"></div>
    </div>

    <!-- Scripts -->
    <script src="{{ mix('manifest.js', 'vendor/nova') }}"></script>
    <script src="{{ mix('vendor.js', 'vendor/nova') }}"></script>
    <script src="{{ mix('app.js', 'vendor/nova') }}"></script>

    <!-- Build Nova Instance -->
    <script>
        const config = @json(\Laravel\Nova\Nova::jsonVariables(request()));
        window.Nova = createNovaApp(config)
        Nova.countdown()
    </script>

    @if ($scripts = \Laravel\Nova\Nova::availableScripts(request()))
        <!-- Tool Scripts -->
        @foreach ($scripts as $asset)
            <script src="{!! $asset->url() !!}"></script>
        @endforeach
    @endif

    <!-- Start Nova -->
    <script defer>
        Nova.liftOff()
    </script>
</body>
</html>

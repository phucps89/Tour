<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel PHP Framework</title>
    {{HTML::style('packages/materialize/css/materialize.min.css')}}
    {{HTML::style('packages/font-awesome/css/font-awesome.min.css')}}
    {{HTML::style('assets/css/style.css')}}
    @yield('resource')
</head>
<body>
    <nav>
        <div class="container nav-wrapper">
            <a href="{{route('home')}}" class="brand-logo">
                <img src="{{asset('assets/media/images/uit.png')}}" />
            </a>
            <ul id="nav-mobile" class="right hide-on-med-and-down">
                <li><a href="sass.html">Sass</a></li>
                <li><a href="badges.html">Components</a></li>
                <li><a href="collapsible.html">JavaScript</a></li>
            </ul>
        </div>
    </nav>

    @yield('content')

    <footer class="page-footer">
        <div class="footer-copyright">
            <div class="container">
                © 2014 Copyright Text
                <a class="grey-text text-lighten-4 right" href="#!">More Links</a>
            </div>
        </div>
    </footer>
    {{HTML::script('assets/js/jquery.min.js')}}
    {{HTML::script('packages/materialize/js/materialize.min.js')}}
    @yield('script')
</body>
</html>

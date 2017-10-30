<!DOCTYPE html>
<html lang="en">
  <head>
    <title>@yield('title')</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- tep icon-->
    <link rel="shortcut icon" href="https://cdn1.iconfinder.com/data/icons/hotel-restaurant/512/1-512.png">

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    {{ Html::style('css/parsley.css') }}
    {{ Html::style('css/styles.css') }}
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/contentstyle.css') }}">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <!-- Scripts -->

    <script>
      window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
      ]) !!};
      /* When the user clicks on the button, 
      toggle between hiding and showing the dropdown content */
      function myFunction() {
          document.getElementById("myDropdown").classList.toggle("show");
      }

      // Close the dropdown if the user clicks outside of it
      window.onclick = function(event) {
        if (!event.target.matches('.dropbtn')) {

          var dropdowns = document.getElementsByClassName("dropdown-content");
          var i;
          for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
              openDropdown.classList.remove('show');
            }
          }
        }
      }
    </script>
    
  </head>
<body>
<div id="app">
  <nav class="navbar  navbar-default" style="background-color: #8deeee ;" role="navigation">
    <div class="container">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <!-- Collapsed Hamburger -->
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
          <span class="sr-only">Toggle Navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="/"><img src="http://logok.org/wp-content/uploads/2015/01/W-Hotels-logo-logotype-1024x768.png" style="margin: -30% 0 0 0; width:50px; height: 50px;"></a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="app-navbar-collapse">
        <!-- Left Side Of Navbar -->
        <ul class="nav navbar-nav">
          <!-- nav bar -->
          <li class="{{ Request::is('/') ? 'active'  : ''}}">         <a href="/">Home</a></li>
          <li class="{{ Request::is('diaries') ? 'active'  : ''}}">   <a href="{{ route('diaries.index') }}"> Diary     </a></li>
          <li class="{{ Request::is('about-us') ? 'active'  : ''}}">  <a href="{{ route('aboutus') }}">       About Us  </a></li>
          <li><a href="{{ route('generateRandomString') }}">Check in Code</a></li>

        </ul>

        <!-- Right Side Of Navbar -->
        <ul class="nav navbar-nav navbar-right">

          <!-- @if (Auth::guest()) -->
            <li><a href="{{ route('login') }}">Login</a></li>
            <li><a href="{{ route('register') }}">Register</a></li>
          <!-- @else -->
            <li><li><a href="{{ route('rooms.create') }}">Host</a></li></li>
            <li><a href="{{ route('mytrips') }}">Trips</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="position: relative; padding-left: 50px;">
                @if (Auth::user()->user_image == NULL)
                  <img src="{{ asset('images/users/blank-profile-picture.png') }}" style="width: 32px; height: 32px; position: absolute; border-radius: 50%; top: 10px; left: 10px;">
                @else
                  <img src="{{ asset('images/users/' . Auth::user()->user_image) }}" style="width: 32px; height: 32px; position: absolute; border-radius: 50%; top: 10px; left: 10px;">
                @endif
                  {{ Auth::user()->user_fname }}<span class="caret"></span>
              </a>

              <!-- <a data-toggle="dropdown" class="dropbtn" style="position: relative;"><img src="{{ asset('images/users/' . Auth::user()->user_image) }}" style="width: 32px; height: 32px; position: absolute; border-radius: 50%; top: 10px; left: 10px;">{{ Auth::user()->user_fname }}<span class="caret"></span></a> -->
              
              <ul class="dropdown-menu" role="menu">
                <li class="text-center">User</li>
                <li><a href="{{ route('users.profile') }}">Profile</a></li>
                <li><a href="{{ route('diaries.mydiaries') }}">My Diary</a></li>
                <li><a href="{{ route('rentals.rmyrooms') }}">Rentals</a></li>
                <li role="separator" class="divider"></li>
                <li class="text-center">Administrator</li>
                <li><a href="{{ route('users.index') }}">Users</a></li>
                <li><a href="{{ route('rooms.index') }}">Rooms</a></li>
                <li><a href="{{ route('categories.index') }}">Categories</a></li>
                <li><a href="{{ route('rentals.index') }}">Trips</li>

                <li role="separator" class="divider"></li>
                <li><a href="{{ route('logout') }}"
                  onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                  </a>
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                  </form></li>
              </ul>
            </li>
          <!-- @endif -->
        </ul>
      </div>
    </div>
  </nav>
</div>
<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>

@include('pages._messages')

@yield('content')


{!! Html::script('js/parsley.min.js') !!}

@include('pages._footer')
</body>
</html>

<header class="clear-both">
	<div class="wrapper">
		<a href="{{ route('home') }}"><h1 class="logo">Love To Travel</h1></a>
		<nav>
			<h2>Main Navigation</h2>
			<ul>
				<li><a href="{{ route('home') }}">Home</a></li>
				<li><a href="{{ route('diaries.index') }}">Diaries</a></li>
				@if(Auth::guest())
				<li><a href="{{ route('helps.index') }}">Help</a></li>
				<li><a href="{{ route('login') }}">Login</a></li>
				<li><a href="{{ route('register') }}">Register</a></li>
				@else
				<li>
					<a href="#">{{ Auth::user()->user_fname }} Account <i class="material-icons">keyboard_arrow_down</i></a>
					<ul class="hav-sub-nav">
						<li><a href="{{ route('users.profile', Auth::user()->id) }}">Profile</a></li>
						<li><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
						<li><a href="{{ route('dashboard.summary.index') }}">Rental Summary</a></li>
						<li><a href="{{ route('helps.index') }}">Help</a></li>
						<li class="dropdown-divider"></li>
						<li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                    	<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        	{{ csrf_field() }}
                    	</form>
					</ul>
				</li>
				@endif
			</ul>
		</nav>
	</div>
</header>

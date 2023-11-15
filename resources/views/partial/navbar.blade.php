<nav class="navbar navbar-expand-lg text-uppercase">
    <div class="container">
      <a class="navbar-brand font-wight-bold" href="#">M2 Rei</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse " id="navbarNavAltMarup">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link px-lg-4 rounded" href="#">About</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-lg-4 rounded" href="#">Resume</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-lg-4 rounded" href="#">Portofolio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-lg-4 rounded" href="#">Blog</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-lg-4 rounded" href="{{ route('posts.index') }}">Gallery</a>
          </li>
        </ul>
      </div>
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ms-auto">
            @guest
            @else
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ Auth::user()->name }}
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
            @endguest
        </ul>
    </div>
    </div>
</nav>

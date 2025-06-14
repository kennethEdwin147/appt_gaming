

<aside class="col-lg-3 split-sidebar">

  <nav class="sticky-top d-none d-lg-block">
    <ul class="nav nav-minimal flex-column" id="toc-nav">

      <li class="nav-item">
        <a class="nav-link fs-lg {{ request()->is('creator/appointments*') ? 'active' : '' }}" href="{{ url('/creator/appointments') }}">
          Rendez-vous
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link fs-lg {{ request()->is('schedule*') ? 'active' : '' }}" href="{{ route('schedule.index') }}">
          Disponibilités
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link fs-lg {{ request()->is('event_type*') ? 'active' : '' }}" href="{{ route('event_type.index') }}">
          Mes événements
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link fs-lg {{ request()->is('creator/public-profile*') ? 'active' : '' }}" href="{{ route('creator.public-profile') }}">
          Page public
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link fs-lg {{ request()->is('creator/payments*') ? 'active' : '' }}" href="{{ url('/creator/payments') }}">
          Paiements
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link fs-lg {{ request()->is('creator/settings*') ? 'active' : '' }}" href="{{ route('creator.settings') }}">
          Paramètres
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link fs-lg text-red" href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
          Déconnexion
        </a>
        <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
        </form>
      </li>
    </ul>
  </nav>
</aside>


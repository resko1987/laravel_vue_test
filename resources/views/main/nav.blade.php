@section('nav')
<div class="container">
    <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
      <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
        <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
        <span class="fs-4">@yield('title')</span>
      </a>

      <ul class="nav nav-pills">
        <li class="nav-item"><a href="/" class="nav-link {{ (request()->is('/')) ? 'active' : '' }}" aria-current="page">Главная</a></li>
        <li class="nav-item"><a href="/employes/" class="nav-link {{ (request()->is('employes')) ? 'active' : '' }}">Сотрудники</a></li>
      </ul>
    </header>
  </div>
@endsection
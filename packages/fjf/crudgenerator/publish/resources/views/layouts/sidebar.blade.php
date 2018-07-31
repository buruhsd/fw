<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="{{asset('asset/dist/img/user2-160x160.jpg')}}" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p>Alexander Pierce</p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    <!-- search form -->
    <form action="#" method="get" class="sidebar-form">
      <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Search...">
        <span class="input-group-btn">
              <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                <i class="fa fa-search"></i>
              </button>
            </span>
      </div>
    </form>
    <!-- /.search form -->
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
      <li class="active treeview menu-open">
        @foreach($fjfMenus->menus as $section)
          @if($section->items)
            <li class="header">{{ $section->section }}</li>
              @foreach($section->items as $menu)
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="{{ url($menu->url) }}">
                        <i class="fa fa-circle-o text-aqua"></i>{{ $menu->title }}
                    </a>
                </li>
              @endforeach
            @endif
        @endforeach
      </li>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>
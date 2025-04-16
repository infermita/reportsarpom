<LINK REL="SHORTCUT ICON"  href="{{ asset('/img/favicon.ico') }}">
<html>
    <head>
        <title>{{ env('APP_NAME') }}</title>    
        <meta http-equiv="Content-Language" content="it-IT" >                        

        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" media="screen" rel="stylesheet" type="text/css" >
        <link href="{{ asset('css/sb-admin-2.min.css') }}" media="screen" rel="stylesheet" type="text/css" >
        <link href="{{ asset('css/datatable/datatables.min.css') }}" media="screen" rel="stylesheet" type="text/css" >
        <link href="{{ asset('css/datatable/buttons.dataTables.min.css') }}" media="screen" rel="stylesheet" type="text/css" >
        <link href="{{ asset('css/custom.css" media="screen" rel="stylesheet') }}" type="text/css" >
        <link href="{{ asset('css/jquery.alerts.css') }}" media="screen" rel="stylesheet" type="text/css" >
        <link href="{{ asset('css/jquery-ui.css') }}" media="screen" rel="stylesheet" type="text/css" >
        <link href="{{ asset('css/custom.css') }}" media="screen" rel="stylesheet" type="text/css" >
        <link href="{{ asset('css/all.min.css') }}" media="screen" rel="stylesheet" type="text/css" >
        <script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/sb-admin-2.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/datatable/dataTables.buttons.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/jquery.alerts.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/jquery-ui.js') }}"></script>    
        <script type="text/javascript" src="{{ asset('js/geco.js') }}"></script>    
    </head>
    @if( request()->path()=='login' )
        <body id="page-top" class="bg-gradient-primary">
    @else
        <body id="page-top">
            <div id="contentLoading" class='load'><img src="/img/ajax-loader.gif" border="0" alt="Caricamento in corso" height="46" width="46"/></div>
            <div id="wrapper">
                <!-- Sidebar -->
                <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

                    <!-- Sidebar - Brand -->
                    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/">
                        {{ env('APP_NAME') }}
                    </a>

                    <!-- Divider -->
                    <hr class="sidebar-divider">

                    <!-- Nav Item - Pages Collapse Menu -->
                    <li class="nav-item">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#config" aria-expanded="false"
                           aria-controls="config">
                            <i class="fas fa-fw fa-cog"></i>
                            <span>Configurazioni</span>
                        </a>
                        <div id="config" class="collapse" aria-labelledby="headingPages"
                             data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                @if ( auth()->user()->roles()->value('name')=='admin' )
                                    <a class="collapse-item " href="/customer">Clienti</a>
                                @endif
                                <a class="collapse-item " href="/device">Device</a>
                                <a class="collapse-item " href="/webapp">Webapp</a>
                            </div>
                        </div>
                    </li>
                    <hr class="sidebar-divider d-none d-md-block">
                    <!-- Sidebar Toggler (Sidebar) -->
                    <div class="text-center d-none d-md-inline">
                        <button class="rounded-circle border-0" id="sidebarToggle"></button>
                    </div>

                </ul>
                <!-- End of Sidebar -->

                <!-- Content Wrapper -->
                <div id="content-wrapper" class="d-flex flex-column">

                    <!-- Main Content -->
                    <div id="content">

                        <!-- Topbar -->
                        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                            <!-- Topbar Navbar -->
                            <ul class="navbar-nav ml-auto">

                                <!-- Nav Item - Messages -->
                                <div class="topbar-divider d-none d-sm-block"></div>

                                <!-- Nav Item - User Information -->
                                <li class="nav-item dropdown no-arrow">
                                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                            {{auth()->user()->name}} ( {{auth()->user()->roles()->value('name')}} )
                                        </span>
                                        <i class="fas fa-fw fa-user-circle"></i>
                                    </a>
                                    <!-- Dropdown - User Information -->
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                         aria-labelledby="userDropdown">
                                        <a class="dropdown-item" href="/logout">
                                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                            Logout
                                        </a>

                                    </div>
                                </li>

                            </ul>

                        </nav>
                        <!-- End of Topbar -->

                        <!-- Begin Page Content -->
                        <div class="container-fluid">
                            <div class="col-md-12 fade-in">
                                <div class="card shadow mb-12">
    @endif




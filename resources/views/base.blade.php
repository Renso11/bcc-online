<!DOCTYPE html>
<html>

<!-- Mirrored from adminlte.io/themes/AdminLTE/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 09 Aug 2022 22:14:43 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Espace admin | @yield('title')</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    
    <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css">

    <link rel="stylesheet" href="/bower_components/Ionicons/css/ionicons.min.css">

    <link rel="stylesheet" href="/dist/css/AdminLTE.min.css">

    <link rel="stylesheet" href="/dist/css/skins/_all-skins.min.css">

    <link rel="stylesheet" href="/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

    <link rel="stylesheet" href="/bower_components/bootstrap-daterangepicker/daterangepicker.css">

    <link rel="stylesheet" href="/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <link rel="stylesheet" href="{{ asset('toastr/toastr.min.css') }}">
    @yield('css')
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <header class="main-header">

            <a href="/" class="logo">
                <img src="/dist/img/logo.svg" style="width: 150px;" class="mr-10" />
            </a>

            <nav class="navbar navbar-static-top">

                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">

                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-user"></i>
                                <span class="hidden-xs">{{ Auth::user()->name . ' ' . Auth::user()->lastname }}</span>
                                <ul class="dropdown-menu">
                            </a>

                            <li class="user-header">
                                <img src="/dist/img/bcb.png" class="img-circle" alt="User Image">
                                <p>
                                    {{ Auth::user()->name . ' ' . Auth::user()->lastname }} - {{Auth::user()->role->libelle}}
                                    <small>Derniere connexion : {{ Auth::user()->last_connexion }} </small>
                                </p>
                            </li>

                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="/profile" class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="#" class="btn btn-default btn-flat" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Déconnexion</a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <aside class="main-sidebar">
            <section class="sidebar">
                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="/dist/img/bcb.png" class="img-circle" alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p>{{ Auth::user()->name . ' ' . Auth::user()->lastname }}</p>
                        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                    </div>
                </div>


                <ul class="sidebar-menu" data-widget="tree">
                    <li @if (Route::currentRouteName() == 'welcome') class="active" @endif>
                        <a href="/">
                            <i class="fa fa-th"></i> <span>Tableau de bord</span>
                        </a>
                    </li>
                    <li class="header">Administration</li>
                    @if (hasPermission('admin.app.client') || hasPermission('admin.app.partenaire'))
                        <li class="treeview @if (in_array(Route::currentRouteName(), ['admin.app.client', 'admin.app.partenaire', 'admin.promotion','admin.tpes'])) active @endif">
                            <a href="#">
                                <i class="fa fa-android"></i> <span>Applications</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                @if (hasPermission('admin.app.client'))
                                    <li class="treeview @if (in_array(Route::currentRouteName(), ['admin.app.client.promotion', 'admin.app.pub', 'admin.app.communication', 'admin.app.client'])) active @endif">
                                        <a href="#"><i class="fa fa-circle-o"></i> Clients
                                            <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                            </span>
                                        </a>
                                        <ul class="treeview-menu">
                                            <li><a href="/app/client" @if (Route::currentRouteName() == 'admin.app.client') class="active" @endif><i class="fa fa-circle-o"></i> Configuration </a></li>
                                            <li><a href="#"><i class="fa fa-circle-o"></i> Communication</a></li>
                                        </ul>
                                    </li>
                                @endif
                                @if (hasPermission('admin.app.partenaire'))
                                    <li class="treeview @if (in_array(Route::currentRouteName(), ['admin.app.tpe', 'admin.app.partenaire','admin.tpes'])) active @endif">
                                        <a href="#"><i class="fa fa-circle-o"></i> Partenaires
                                            <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                            </span>
                                        </a>
                                        <ul class="treeview-menu">
                                            <li><a href="/app/partenaire" @if (Route::currentRouteName() == 'admin.app.partenaire') class="active" @endif><i class="fa fa-circle-o"></i> Configuration </a></li>
                                            <li><a href="/tpes" @if (Route::currentRouteName() == 'admin.tpes') class="active" @endif><i class="fa fa-circle-o"></i> Gestion TPE</a></li>
                                            <li><a href="#"><i class="fa fa-circle-o"></i> Communication</a></li>
                                        </ul>
                                    </li>
                                @endif
                                @if (hasPermission('admin.app.admin'))
                                    <li @if (Route::currentRouteName() == 'admin.app.admin') class="active" @endif>
                                        <a href="#"><i class="fa fa-circle-o"></i> Admin</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if (hasPermission('admin.users') || hasPermission('admin.roles') || hasPermission('admin.permissions'))
                        <li class="treeview @if (in_array(Route::currentRouteName(), ['admin.users', 'admin.roles','admin.permissions'])) active @endif">
                            <a href="#">
                                <i class="fa fa-users"></i> <span>Utilisateurs</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                @if (hasPermission('admin.users'))
                                    <li @if (Route::currentRouteName() == 'admin.users') class="active" @endif><a href="/users"><i class="fa fa-circle-o"></i> Liste</a></li>
                                @endif
                                @if (hasPermission('admin.roles'))
                                    <li @if (Route::currentRouteName() == 'admin.roles') class="active" @endif><a href="/roles"><i class="fa fa-circle-o"></i> Roles</a></li>
                                @endif
                                @if (hasPermission('admin.permissions'))
                                    <li @if (Route::currentRouteName() == 'admin.permissions') class="active" @endif><a href="/permissions"><i class="fa fa-circle-o"></i> Permissions </a></li>
                                    <li @if (Route::currentRouteName() == 'admin.permissions') class="active" @endif><a href="/apporteurs"><i class="fa fa-circle-o"></i> Apporteurs </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if (hasPermission('admin.frais') || hasPermission('admin.restrictions'))
                        <li class="treeview @if (in_array(Route::currentRouteName(), ['admin.frais', 'admin.restrictions'])) active @endif">
                            <a href="#">
                                <i class="fa fa-cog"></i> <span>Configurations</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                @if (hasPermission('admin.frais'))
                                    <li @if (Route::currentRouteName() == 'admin.frais') class="active" @endif><a href="/frais"><i class="fa fa-circle-o"></i> Frais de transactions</a></li>
                                @endif
                                @if (hasPermission('admin.restrictions'))
                                    <li @if (Route::currentRouteName() == 'admin.restrictions') class="active" @endif><a href="/restrictions"><i class="fa fa-circle-o"></i> Restrictions</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    
                    @if (hasPermission('admin.promo.partenaires') && hasPermission('admin.promo.clients'))
                        <li class="treeview @if (in_array(Route::currentRouteName(), ['admin.promotion'])) active @endif">
                            <a href="#">
                                <i class="fa fa-android"></i> <span> Promotions </span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                @if (hasPermission('admin.promo.clients'))
                                    <li @if (Route::currentRouteName() == 'admin.promotion.clients') class="active" @endif>
                                        <a href="/promotion/clients"><i class="fa fa-circle-o"></i> Clients </a>
                                    </li>
                                @endif
                                @if (hasPermission('admin.promo.partenaires'))
                                    <li @if (Route::currentRouteName() == 'admin.promotion.partenaires') class="active" @endif>
                                        <a href="/promotion/partenaires"><i class="fa fa-circle-o"></i> Partenaires </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if (hasPermission('admin.transfert'))
                        <li>
                            <a href="/compte/commission" @if (Route::currentRouteName() == 'admin.transfert') class="active" @endif>
                                <i class="fa fa-money"></i> <span>Compte de commission</span>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('admin.transfert'))
                        <li>
                            <a href="/transfert/admin" @if (Route::currentRouteName() == 'admin.transfert') class="active" @endif>
                                <i class="fa fa-money"></i> <span>Transfert Admin</span>
                            </a>
                        </li>
                    @endif

                    <li class="header">Exploitation</li>
                    @if (hasPermission('admin.clients') || hasPermission('admin.client.operations.attentes'))
                        <li class="treeview @if(in_array(Route::currentRouteName(), ['admin.clients','admin.client.operations.attentes'])) active @endif">
                            <a href="#">
                                <i class="fa fa-users"></i> <span>Clients</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                @if (hasPermission('admin.clients'))
                                    <li @if (Route::currentRouteName() == 'admin.clients') class="active" @endif><a href="/clients"><i class="fa fa-circle-o"></i> Liste des comptes</a></li>
                                    <li @if (Route::currentRouteName() == 'admin.clients.attentes') class="active" @endif><a href="/clients/attentes"><i class="fa fa-circle-o"></i> Comptes attentes</a></li>
                                    <li @if (Route::currentRouteName() == 'admin.clients.rejetes') class="active" @endif><a href="/clients/rejetes"><i class="fa fa-circle-o"></i> Compte rejetés</a></li>
                                    <li @if (Route::currentRouteName() == 'admin.clients.non.completes') class="active" @endif><a href="/clients/non/completes"><i class="fa fa-circle-o"></i> Compte non complétés</a></li>
                                @endif
                                @if (hasPermission('admin.client.operations.attentes'))
                                    <li @if (Route::currentRouteName() == 'admin.client.operations.attentes') class="active" @endif><a href="/clients/operations/attentes"><i class="fa fa-circle-o"></i> Opérations en attentes</a></li>
                                    <li @if (Route::currentRouteName() == 'admin.client.operations.finalisees') class="active" @endif><a href="/clients/operations/finalisees"><i class="fa fa-circle-o"></i> Opérations finalisées</a></li>
                                    <li @if (Route::currentRouteName() == 'admin.client.operations.remboursees') class="active" @endif><a href="/clients/operations/remboursees"><i class="fa fa-circle-o"></i> Opérations remboursées</a></li>
                                    <li @if (Route::currentRouteName() == 'admin.client.operations.annulees') class="active" @endif><a href="/clients/operations/annulees"><i class="fa fa-circle-o"></i> Opérations annulées</a></li>
                                    <li @if (Route::currentRouteName() == 'admin.client.paiements') class="active" @endif><a href="/clients/paiements"><i class="fa fa-circle-o"></i> Paiements </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if (hasPermission('admin.partenaires') || hasPermission('admin.partenaire.recharge.attentes'))
                        <li class="treeview @if(in_array(Route::currentRouteName(), ['admin.partenaires', 'admin.partenaire.recharge.attentes'])) active @endif">
                            <a href="#">
                                <i class="fa fa-pie-chart"></i> <span>Partenaires</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                @if (hasPermission('admin.partenaires'))
                                    <li @if (Route::currentRouteName() == 'admin.partenaire.new') class="active" @endif><a href="/partenaire/new"><i class="fa fa-circle-o"></i> Nouveau partenaire</a></li>
                                    <li @if (Route::currentRouteName() == 'admin.partenaires') class="active" @endif><a href="/partenaires"><i class="fa fa-circle-o"></i> Liste des partenaires</a></li>
                                @endif
                                @if (hasPermission('admin.partenaire.recharge.attentes'))
                                    <li @if (Route::currentRouteName() == 'admin.partenaire.recharge.attentes') class="active" @endif><a href="/partenaire/recharges/attentes"><i class="fa fa-circle-o"></i> Rechargement en attentes</a></li>
                                    <li @if (Route::currentRouteName() == 'admin.partenaire.operations.attentes') class="active" @endif><a href="/partenaire/operations/attentes"><i class="fa fa-circle-o"></i> Opérations attentes</a></li>
                                    <li @if (Route::currentRouteName() == 'admin.partenaire.operations.finalisees') class="active" @endif><a href="/partenaire/operations/finalises"><i class="fa fa-circle-o"></i> Opérations finalisees</a></li>
                                    <li @if (Route::currentRouteName() == 'admin.partenaire.operations.remboursees') class="active" @endif><a href="/partenaire/operations/remboursees"><i class="fa fa-circle-o"></i> Opérations remboursées</a></li>
                                    <li @if (Route::currentRouteName() == 'admin.partenaire.operations.annulees') class="active" @endif><a href="/partenaire/operations/annulees"><i class="fa fa-circle-o"></i> Opérations annulées</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    <li class="treeview @if(in_array(Route::currentRouteName(), ['admin.compte.all.partner', 'admin.compte.commission.detail'])) active @endif">
                        <a href="#">
                            <i class="fa fa-money"></i> <span>Compte de mouvement</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if (hasPermission('admin.compte.all.partner'))
                                <li @if (Route::currentRouteName() == 'admin.compte.all.partner') class="active" @endif><a href="/compte/all/partner"><i class="fa fa-circle-o"></i> Mouvements des partenaires</a>
                                </li>
                            @endif
                            @if (hasPermission('admin.compte.solde'))
                                <li @if (Route::currentRouteName() == 'admin.compte.solde') class="active" @endif><a href="/compte/solde"><i class="fa fa-circle-o"></i> Solde des comptes mvts</a></li>
                            @endif
                            @if (hasPermission('admin.compte.commission.detail'))
                                @foreach ($compteCommissions as $item)
                                    <li @if (Route::currentRouteName() == 'admin.compte.commission.detail') class="active" @endif><a href="/compte/commission/detail/{{$item->id}}"><i class="fa fa-circle-o"></i> Compte commission {{ $item->libelle }}</a></li>
                                @endforeach
                            @endif
                        </ul>
                    </li>
                    
                    <li class="header">Rapport</li>
                    @if (hasPermission('admin.rapport'))
                        <li>
                            <a href="/rapport/achat/cartes">
                                <i class="fa fa-file-pdf-o"></i> <span>Achat de carte</span>
                            </a>
                        </li>
                        <li>
                            <a href="/rapport/transactions/clients">
                                <i class="fa fa-file-pdf-o"></i> <span>Opérations des clients</span>
                            </a>
                        </li>
                        <li>
                            <a href="/rapport/transactions/partenaires">
                                <i class="fa fa-file-pdf-o"></i> <span>Opérations des partenaires</span>
                            </a>
                        </li>
                        <li>
                            <a href="/rapport/transactions/apporteurs">
                                <i class="fa fa-file-pdf-o"></i> <span>Opérations des apporteurs</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </section>

        </aside>

        <div class="content-wrapper">

            <section class="content-header">
                <h1>
                    @yield('page')
                </h1>
                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> BCC Admin </a></li>
                    <li class="active">@yield('page')</li>
                </ol>
            </section>
            @yield('content')
        </div>

        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 2.4.13
            </div>
            <strong>Copyright &copy; {{date('Y')}} <a href="#">ELG</a>.</strong> All rights
            reserved.
        </footer>
        <div class="control-sidebar-bg"></div>
    </div>


    <script src="/bower_components/jquery/dist/jquery.min.js"></script>

    <script src="/bower_components/jquery-ui/jquery-ui.min.js"></script>

    <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <script src="/bower_components/moment/min/moment.min.js"></script>
    <script src="/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>

    <script src="/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <script src="/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

    <script src="/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>

    <script src="/bower_components/fastclick/lib/fastclick.js"></script>

    <script src="/dist/js/adminlte.min.js"></script>
    
    <script src="{{ asset('toastr/toastr.min.js') }}"></script>
    <script>
        $(function() {
            @if (session()->has('success'))
                toastr.success("{{ session()->get('success') }}")
            @endif
            @if (session()->has('warning'))
                toastr.warning("{{ session()->get('warning') }}")
            @endif
            @if (session()->has('error'))
                toastr.error("{{ session()->get('error') }}")
            @endif
        }); 
    </script>

    @yield('js')
</body>

<!-- Mirrored from adminlte.io/themes/AdminLTE/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 09 Aug 2022 22:15:21 GMT -->

</html>

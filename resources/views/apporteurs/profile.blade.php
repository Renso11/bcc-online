<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 2 | Top Navigation</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, apporteur-scalable=no" name="viewport">

    <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css">

    <link rel="stylesheet" href="/bower_components/Ionicons/css/ionicons.min.css">

    <link rel="stylesheet" href="/dist/css/AdminLTE.min.css">

    <link rel="stylesheet" href="/dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="/plugins/intl-tel-input-master/build/css/intlTelInput.css" />
    <link rel="stylesheet" href="/plugins/intl-tel-input-master/build/css/demo.css" />


    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <link rel="stylesheet" href="{{ asset('toastr/toastr.min.css') }}">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <script nonce="d8fbaf63-3af4-4685-bfc3-b0f9b025084a">
        try {
            (function(w, d) {
                ! function(bS, bT, bU, bV) {
                    bS[bU] = bS[bU] || {};
                    bS[bU].executed = [];
                    bS.zaraz = {
                        deferred: [],
                        listeners: []
                    };
                    bS.zaraz._v = "5629";
                    bS.zaraz.q = [];
                    bS.zaraz._f = function(bW) {
                        return async function() {
                            var bX = Array.prototype.slice.call(arguments);
                            bS.zaraz.q.push({
                                m: bW,
                                a: bX
                            })
                        }
                    };
                    for (const bY of ["track", "set", "debug"]) bS.zaraz[bY] = bS.zaraz._f(bY);
                    bS.zaraz.init = () => {
                        var bZ = bT.getElementsByTagName(bV)[0],
                            b$ = bT.createElement(bV),
                            ca = bT.getElementsByTagName("title")[0];
                        ca && (bS[bU].t = bT.getElementsByTagName("title")[0].text);
                        bS[bU].x = Math.random();
                        bS[bU].w = bS.screen.width;
                        bS[bU].h = bS.screen.height;
                        bS[bU].j = bS.innerHeight;
                        bS[bU].e = bS.innerWidth;
                        bS[bU].l = bS.location.href;
                        bS[bU].r = bT.referrer;
                        bS[bU].k = bS.screen.colorDepth;
                        bS[bU].n = bT.characterSet;
                        bS[bU].o = (new Date).getTimezoneOffset();
                        if (bS.dataLayer)
                            for (const ce of Object.entries(Object.entries(dataLayer).reduce(((cf, cg) => ({
                                    ...cf[1],
                                    ...cg[1]
                                })), {}))) zaraz.set(ce[0], ce[1], {
                                scope: "page"
                            });
                        bS[bU].q = [];
                        for (; bS.zaraz.q.length;) {
                            const ch = bS.zaraz.q.shift();
                            bS[bU].q.push(ch)
                        }
                        b$.defer = !0;
                        for (const ci of [localStorage, sessionStorage]) Object.keys(ci || {}).filter((ck => ck
                            .startsWith("_zaraz_"))).forEach((cj => {
                            try {
                                bS[bU]["z_" + cj.slice(7)] = JSON.parse(ci.getItem(cj))
                            } catch {
                                bS[bU]["z_" + cj.slice(7)] = ci.getItem(cj)
                            }
                        }));
                        b$.referrerPolicy = "origin";
                        b$.src = "/cdn-cgi/zaraz/s.js?z=" + btoa(encodeURIComponent(JSON.stringify(bS[bU])));
                        bZ.parentNode.insertBefore(b$, bZ)
                    };
                    ["complete", "interactive"].includes(bT.readyState) ? zaraz.init() : bS.addEventListener(
                        "DOMContentLoaded", zaraz.init)
                }(w, d, "zarazData", "script");
            })(window, document)
        } catch (e) {
            throw fetch("/cdn-cgi/zaraz/t"), e;
        };
    </script>
</head>

<body class="hold-transition skin-blue layout-top-nav">
    <div class="wrapper">
        <header class="main-header">
            <nav class="navbar navbar-static-top">
                <div class="container">
                    <div class="navbar-header">
                        <a href="/apporteur/dashboard" class="navbar-brand"><b>BCC</b>Apporteur</a>
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#navbar-collapse">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>



                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li class="dropdown apporteur apporteur-menu">

                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-apporteur fa-2x"></i>

                                    <span class="hidden-xs">{{ $apporteur->lastname.' '.$apporteur->name }}</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="apporteur-footer">
                                        <div class="pull-left">
                                            <a href="/apporteur/profile" class="btn btn-default btn-flat">Profile</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="/apporteur/logout" class="btn btn-default btn-flat">Deconnexion</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                </div>

            </nav>
        </header>

        <div class="content-wrapper">
            <div class="container">
                <section class="content">
                    <div class="row">
                        <div class="col-md-3">
                
                            <div class="box box-primary">
                                <div class="box-body box-profile">
                                    <h3 class="profile-apporteurname text-center">{{ $apporteur->name . ' ' . $apporteur->lastname }}</h3>
                                    <ul class="list-group list-group-unbordered">
                                        <li class="list-group-item">
                                            <b>Username</b> <a class="pull-right">{{ '@'.$apporteur->telephone }}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Code Promo</b> <a class="pull-right">{{ $apporteur->code_promo }}</a>
                                        </li>
                                    </ul>
                                    <a class="btn btn-success" href="/regenerate/apporteur/promo/{{$apporteur->id}}">Regenerer le code</a>
                                </div>
                            </div>
                
                        </div>
                
                        <div class="col-md-9">
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#info" data-toggle="tab">Informations</a></li>
                                    <li><a href="#pass" data-toggle="tab">Mot de passe</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="active tab-pane" id="info">
                                        <form action="/profile/apporteur/edit/{{$apporteur->id}}" method="POST" class="form-horizontal form-material">
                                            @csrf
                                            <div class="form-group">
                                                <label class="col-md-12">Nom</label>
                                                <div class="col-md-12">
                                                    <input type="text" placeholder="Nom" name="name" value="{{ $apporteur->name }}" class="form-control form-control-line">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-12">Prenom</label>
                                                <div class="col-md-12">
                                                    <input type="text" placeholder="Prenom" name="lastname" value="{{ $apporteur->lastname }}" class="form-control form-control-line">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="example-email" class="col-md-12">Telephone</label>
                                                <div class="col-md-12">
                                                    <input type="text" placeholder="Telephone" name="telephone" value="{{ $apporteur->telephone }}" class="form-control form-control-line"  id="example-email">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <button class="btn btn-success">Modifier</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                
                                    <div class="tab-pane" id="pass">
                                        <form action="/reset/apporteur/password/{{$apporteur->id}}" id="form-change-password" method="POST" class="form-horizontal form-material">
                                            @csrf
                                            <div class="form-group">
                                                <label class="col-md-12">Nouveau mot de passe</label>
                                                <div class="col-md-12">
                                                    <input type="password" id="password" name="password" placeholder="Nouveau mot de passe" class="form-control form-control-line">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="example-email" class="col-md-12">Confirmer le mot de passe</label>
                                                <div class="col-md-12">
                                                    <input type="password" id="conf-password" placeholder="Confirmer le mot de passe" class="form-control form-control-line" name="example-email" id="example-email">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <button type="submit" class="btn btn-success" id="change-password">Modifier</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

        </div>
    </div>


    <script src="/bower_components/jquery/dist/jquery.min.js"></script>

    <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <script src="/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>

    <script src="/bower_components/fastclick/lib/fastclick.js"></script>

    <script src="/dist/js/adminlte.min.js"></script>

    <script src="/dist/js/demo.js"></script>
    <script src="/plugins/select2/js/select2.full.min.js"></script>

    <script src="/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script>
        $(".example1").DataTable({
            ordering: false
        });

        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })
    </script>
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
</body>

</html>

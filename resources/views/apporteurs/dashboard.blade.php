<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 2 | Top Navigation</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

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
                            <li class="dropdown user user-menu">

                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-user fa-2x"></i>

                                    <span class="hidden-xs">{{ $apporteur->lastname.' '.$apporteur->name }}</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="user-footer">
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
                    <br>
                    <div class="row">
                        <div class="col-lg-12 col-xs-12">
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>{{ $solde }} XOF </h3>
                                    <p>Compte commission </p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-money"></i>
                                </div>
                                <a href="#" data-target="#modal-retrait" class="small-box-footer" data-toggle="modal"> <i class="fa fa-minus"></i> &nbsp; Recuperer ma commission</a>
                            </div>
                        </div>
                    </div>
            
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Mouvement du compte de commission </h3>
                                </div>
                
                                <div class="box-body table-responsive">
                                    <table class="table table-bordered table-striped example1">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Libelle</th>
                                                <th>Montant</th>
                                                <th>Sens</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($operations as $item)
                                                <tr>
                                                    <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                                                    <td>{{ $item->libelle }}</td>
                                                    <td>{{ $item->montant }}</td>
                                                    <td class="text-capitalize">{{ $item->sens }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Carte activées </h3>
                                </div>
                
                                <div class="box-body table-responsive">                        
                                    <table class="table table-bordered table-striped example1">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Client</th>
                                                <th>Telephone</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($activations as $item)
                                                <tr>
                                                    <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                                                    <td>{{ $item->userCard ? $item->userCard->userClient->lastname.' '.$item->userCard->userClient->name : '-' }}</td>
                                                    <td>{{ $item->userCard ? $item->userCard->userClient->username : '-'}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

        </div>

        <div class="modal fade" id="modal-retrait" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Retrait sur compte de comission</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="/retrait/apporteur/{{ $apporteur->id }}" method="POST"> 
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">Moyen de paiement</label>
                                <select required class="form-control select2bs4" required name="moyen" id="moyen"  data-placeholder="Selectionner le moyen de paiement">
                                    <option value="">Selectionner le moyen de paiement</option>
                                    <option value="bcv">BCC</option>
                                    <option value="bmo">BMO</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">Montant:</label>
                                <input type="number" required class="form-control" name="amount">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Retirer</button>
                        </div>
                    </form>
                </div>
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

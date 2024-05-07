@extends('base')
@section('title')
    Accueil
@endsection
@section('page')
    Accueil
@endsection
@section('css')
    <link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
@endsection
@section('content')
    <section class="content">
            <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ $solde['gtp'] }} <sup style="font-size: 20px">XOF</sup> </h3>
                        <p>Solde GTP</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="#" class="small-box-footer">Détails <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{ $solde['bmo_debit'] }} XOF <sup style="font-size: 20px">XOF</sup></h3>
                        <p>Solde BCV Cash Collect (Debit)</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="#" class="small-box-footer">Détails <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{ $solde['bmo_credit'] }} <sup style="font-size: 20px">XOF</sup></h3>
                        <p>Solde Virtual Load (Credit)</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="#" class="small-box-footer">Détails <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ $solde['kkiapay'] }} <sup style="font-size: 20px">XOF</sup> </h3>
                        <p>Solde Kkiapay</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="#" class="small-box-footer">Recharger le compte <i class="fa fa-plus"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{ $solde['compte_partenaire'] }} <sup style="font-size: 20px">XOF</sup> </h3>
                        <p>Solde total compte partenaire</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="#" class="small-box-footer">Détails <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

    </section>
@endsection

@section('js')
    <script src="/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

    <script src="https://cdn.kkiapay.me/k.js"></script>
    <script>
        $(".example1").DataTable({
            ordering: false
        });

        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

        $('#pay').on('click', function(e) {
            e.preventDefault()

            var montant = $("#montant").val();
            openKkiapayWidget({
                amount: montant,
                position: "center",
                sandbox: "false",
                theme: "#975102",
                key: "653a4b85df3c403ad1fb39a64cc9a9ef874432db"
            })
            addSuccessListener(response => {
                $('#reference').val(response.transactionId)
                $('#form-recharge-kkp').submit();
            });
        })
    </script>
@endsection

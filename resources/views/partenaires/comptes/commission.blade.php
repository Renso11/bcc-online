@extends('base')
@section('css')
    <link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endsection
@section('content')
    <section class="content">
        <br>
        <div class="row">
            <div class="col-lg-12 col-xs-12">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>{{ $compteCommission->solde }} XOF</h3>
                        <p>Compte commission {{ $compteCommission->partenaire->libelle }}</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="#" data-target="#modal-depot" class="small-box-footer" data-toggle="modal">Vider le compte <i class="fa fa-minus"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Mouvement du compte de commission </h3>
                    </div>

                    <div class="box-body">
                        <table class="table table-bordered table-striped example1">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Libelle</th>
                                    <th class="text-capitalize">Type</th>
                                    <th>Date</th>
                                    <th>Solde avant</th>
                                    <th>Montant</th>
                                    <th>Solde apres</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($operationsCompteCommission as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->libelle }}</td>
                                        <td>{{ $item->type }}</td>
                                        <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                        <td>{{ $item->solde_avant }} F CFA</td>
                                        <td>{{ $item->montant }} F CFA</td>
                                        <td>{{ $item->solde_apres }} F CFA</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <!--This page plugins -->
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

        //$('#checkAll').on('click',function(e))
    </script>
@endsection
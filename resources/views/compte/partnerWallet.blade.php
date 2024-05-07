

@extends('base')
@section('title')
    Compte de gestion des operations partenaires
@endsection
@section('page')
    Compte de gestion des operations partenaires
@endsection
@section('css')
    <link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
@endsection
@section('content')
    <section class="content">

        <div class="row">
            <div class="col-lg-12 col-xs-12">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>{{ $partnerWalletAll->solde }}</h3>
                        <p>Compte gestion partenaires</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="#" data-target="#modal-depot" class="small-box-footer" data-toggle="modal">Approvisionner le compte <i class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Mouvement du compte </h3>
                    </div>
    
                    <div class="box-body">
                        <table class="table table-bordered table-striped example1">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Auteur</th>
                                    <th>Reference</th>
                                    <th class="text-capitalize">Type</th>
                                    <th>Montant <small>(F cfa)</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($partnerWalletAllDetails as $item)
                                    <tr>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $item->partenaire ? $item->partenaire->libelle : 'Administrateur' }}</td>
                                        <td>{{ $item->reference }}</td>
                                        <td class="text-capitalize">{{ $item->sens }}</td>
                                        <td>{{ $item->amount }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-depot">
            <div class="modal-dialog">
                <form action="/compte/all/partner/recharge" id="form-recharge" method="post">
                    @csrf
                    <input type="hidden" name="cartes" id="cartes">
                    <div class="modal-content">
                        <div class="modal-header">
                            <span>Rechargement de compte</span>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="number" class="form-control" id="montant-recharge" name="montant" placeholder="Montant">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="text"  placeholder="Reference bancaire" class="form-control" name="reference" id="reference">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" id="btn-recharge" class="btn btn-primary" style="width:100%">Recharger</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

    </section>

@endsection

@section('js')
    <script src="/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

    <script>
        $(".example1").DataTable({
            ordering: false
        });
    </script>
@endsection

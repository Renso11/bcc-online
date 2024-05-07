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
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <form action="/recovery-database" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="revovery" id="">
                        <button type="submit">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
@endsection



<div class="modal fade" id="show-transaction-{{ $item['id'] }}" tabindex="-1" role="dialog"
aria-labelledby="exampleModalLabel1">
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLabel1">Détails de la transaction</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <div class="row">
                @if($item['type'] == 'Depot')
                    <div class="col-md-4">
                        <label for="">Date d'operation</label>
                        <p>{{ $item['date'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Nom et prénoms</label>
                        <p>{{ $item['userClient']->name.' '.$item['userClient']->lastname }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Telephone</label>
                        <p>{{ $item['userClient'] ? '+'.$item['userClient']->username : '' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Type d'opération</label>
                        <p>{{ $item['type'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Methode de paiement</label>
                        <p>{{ $item['moyen_paiement'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Reference du paiement</label>
                        <p>{{ $item['reference_operateur'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Montant</label>
                        <p>{{ $item['montant'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Frais</label>
                        <p>{{ $item['frais'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Status du paiement</label>
                        <p>{{ $item['is_debited'] == 1 ? 'Payée' : 'Non payée' }}</p>
                    </div>
                @elseif($item['type'] == 'Transfert')
                    <div class="col-md-4">
                        <label for="">Date d'operation</label>
                        <p>{{ $item['date'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Nom et prénoms</label>
                        <p>{{ $item['userClient'] ? $item['userClient']->name.' '.$item['userClient']->lastname : '' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Telephone</label>
                        <p>{{ $item['userClient'] ? '+'.$item['userClient']->username : '' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Type d'opération</label>
                        <p>{{ $item['type'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Methode de paiement</label>
                        <p>{{ $item['moyen_paiement'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Reference du debit</label>
                        <p>{{ $item['reference_gtp_debit'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Montant</label>
                        <p>{{ $item['montant'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Frais</label>
                        <p>{{ $item['frais'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Status du debit</label>
                        <p>{{ $item['is_debited'] == 1 ? 'Carte débitée' : 'Carte non débitée' }}</p>
                    </div>
                @else
                    <div class="col-md-4">
                        <label for="">Date d'operation</label>
                        <p>{{ $item['date'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Nom et prénoms</label>
                        <p>{{ $item['userClient'] ? $item['userClient']->name.' '.$item['userClient']->lastname : '' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Telephone</label>
                        <p>{{ $item['userClient'] ? '+'.$item['userClient']->username : '' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Type d'opération</label>
                        <p>{{ $item['type'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Methode de paiement</label>
                        <p>{{ $item['moyen_paiement'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Reference du paiement</label>
                        <p>{{ $item['reference_paiement'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Montant</label>
                        <p>{{ $item['montant'] }}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Status du paiement</label>
                        <p>{{ $item['is_debited'] == 1 ? 'Payée' : 'Non payée' }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="complete-transaction-{{ $item['id'] }}" tabindex="-1" role="dialog"
aria-labelledby="exampleModalLabel1">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLabel1">Finalisation de la transaction</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form action="/clients/operations/attentes/complete" method="POST">
            @csrf
            <div class="modal-body">
                <input type="hidden" name="id" value="{{ $item['id']}}">
                <input type="hidden" name="type_operation" value="{{ $item['type']}}">
                <input type="hidden" name="moyen_paiement" value="{{ $item['moyen_paiement']}}">
                <p>Etes vous sur de finaliser cette transaction?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                <button type="submit" class="btn btn-primary">Oui</button>
            </div>
        </form>
    </div>
</div>
</div>

<div class="modal fade" id="refund-transaction-{{ $item['id'] }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Remboursement de la transaction</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <form action="/clients/operations/attentes/refund" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Etes vous sur de rembourser cette transaction?</p>
                    <input type="hidden" name="id" value="{{ $item['id']}}">
                    <input type="hidden" name="type_operation" value="{{ $item['type']}}">
                    <input type="hidden" name="moyen_paiement" value="{{ $item['moyen_paiement']}}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                    <button type="submit" class="btn btn-primary">Oui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cancel-transaction-{{ $item['id'] }}" tabindex="-1" role="dialog"
aria-labelledby="exampleModalLabel1">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLabel1">Annulation de la transaction</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <form action="/clients/operations/attentes/cancel" method="POST">
            @csrf
            <div class="modal-body">
                <p>Etes vous sur d'annuler cette transaction?</p>
                <input type="hidden" name="id" value="{{ $item['id']}}">
                <input type="hidden" name="type_operation" value="{{ $item['type']}}">
                <input type="hidden" name="moyen_paiement" value="{{ $item['moyen_paiement']}}">
                <label for="">Motif de l'annulation</label>
                <textarea name="motif_cancel" class="form-control" rows="8"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                <button type="submit" class="btn btn-primary">Oui</button>
            </div>
        </form>
    </div>
</div>
</div>
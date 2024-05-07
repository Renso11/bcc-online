@extends('base')
@section('css')
    <link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endsection
@section('page')
    Ventes partenaires en attente de validation
@endsection
@section('content')
<section class="content">  
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0">
                    <h3 class="card-title">Liste des ventes partenaires en attente de validation</h3>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped" id="example1">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Partenaire</th>
                                    <th>Montant</th>
                                    <th>Nombre</th>
                                    <th>Initiateur</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ventes as $item)
                                    <tr>
                                        <td>{{ $item->created_at }} </td>
                                        <td>{{ $item->partenaire->libelle }} </td>
                                        <td>{{ $item->montant }} </td>
                                        <td>{{ $item->nombre }} </td>
                                        <td>{{ $item->user->name. ' ' .$item->user->lastname }}</td>
                                        <td>            
                                            <button type="button" class="btn btn-success"  data-toggle="modal" data-target="#vente-partenaire-{{ $item->id }}">
                                                <i class="fa fa-check"></i> Valider
                                            </button>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="vente-partenaire-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Validation de vente de carte virtuelle</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/partenaire/valide/vente/{{ $item->id }}" method="POST"> 
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Etes vous sur de valider cette vente de <b>{{ $item->nombre }}</b> cartes d'un montant de <b>{{ $item->montant }}</b> F CFA vers le partenaire <b>{{ $item->partenaire->libelle }}</b> ?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                        <button type="submit" class="btn btn-primary">Oui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
    </script>
@endsection
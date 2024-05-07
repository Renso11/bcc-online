@extends('base')
@section('css')
<link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
@endsection
@section('title')
    Rechargements partenaires en attente
@endsection
@section('page')
    Rechargements partenaires en attente
@endsection
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Liste des rechargements partenaires en attente</h3>
                    </div>

                    <div class="box-body">
                        <table class="table table-bordered table-striped example1">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Partenaire</th>
                                    <th>Montant</th>
                                    <th>Initiateur</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recharges as $item)
                                    <tr>
                                        <td>{{ $item->created_at }} </td>
                                        <td>{{ $item->partenaire->libelle }} </td>
                                        <td>{{ $item->montant }} </td>
                                        <td>{{ $item->user->name. ' ' .$item->user->lastname }}</td>
                                        <td>
                                            <button type="button" class="btn btn-success"  data-toggle="modal" data-target="#recharge-partenaire-{{ $item->id }}">
                                                <i class="fa fa-check"></i> Valider
                                            </button>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="recharge-partenaire-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog modal-sm" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Validation de rechargement</h4>
                                                </div>
                                                <form action="/partenaire/valide/recharge/{{ $item->id }}" method="POST"> 
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Etes vous sur de valider ce rechargement de <b>{{ $item->montant }}</b> F CFA vers le partenaire <b>{{ $item->partenaire->libelle }}</b> ?</p>
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
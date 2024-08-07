@extends('base')
@section('css')
    <link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endsection
@section('page')
    Liste des restrictions appliquées
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @if (hasPermission('admin.restrictions'))
                        <button type="button" class="btn waves-effect waves-light btn-primary" data-toggle="modal" data-target="#add-gamme"> Ajouter une restriction </button>
                        <br>
                        <br>
                    @endif
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Frais de transactions</h3>
                        </div>
    
                        <div class="box-body">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Type d'acteur</th>
                                        <th>Type d'operation</th>
                                        <th>Type de restriction</th>
                                        <th>Valeur</th>
                                        <th>Periode</th>
                                        <th>Etat</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($restrictions as $item)
                                        <tr>
                                            <td>{{ ucwords($item->type_acteur) }}</td>
                                            <td>{{ ucwords($item->type_operation) }}</td>
                                            <td>{{ ucwords($item->type_restriction) }}</td>
                                            <td>{{ $item->valeur }}</td>
                                            <td>{{ ucwords($item->periode) }}</td>
                                            <td>@if($item->etat == 1) Active @else Desactivate @endif</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default btn-flat">Actions</button>
                                                    <button type="button" class="btn btn-default btn-flat dropdown-toggle"
                                                        data-toggle="dropdown">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#edit-restriction{{ $item->id }}"><i class="fa fa-edit"></i>&nbsp;&nbsp;Modifier les informations</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#del-restriction{{ $item->id }}"><i class="fa fa-trash"></i>&nbsp;&nbsp;Supprimer la restriction</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#act-restriction{{ $item->id }}"><i class="fa fa-check"></i>&nbsp;&nbsp;Activer la restriction</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#des-restriction{{ $item->id }}"><i class="fa fa-times"></i>&nbsp;&nbsp;Desactiver la restriction</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="edit-restriction{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="exampleModalLabel1">Modification de la restriction </h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <form action="/restrictions/edit/{{ $item->id }}" method="POST" id="form-edit-{{ $item->id }}"> 
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="">Type d'acteur</label>
                                                                            <select class="form-control select2bs4 type" name="type_acteur" id="" style="width:100%">
                                                                                <option value="">Selectionner un type d'acteur</option>
                                                                                <option @if($item->type_acteur == 'client') selected @endif value="client">Client</option>
                                                                                <option @if($item->type_acteur == 'partenaire') selected @endif value="partenaire">Partenaire</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="">Type operation</label>
                                                                            <select class="form-control select2bs4 type" name="type_operation" id="" style="width:100%">
                                                                                <option value="">Selectionner un type d'operation</option>
                                                                                <option @if($item->type_operation == 'depot') selected @endif value="depot">Depot</option>
                                                                                <option @if($item->type_operation == 'retrait') selected @endif value="retrait">Retrait</option>
                                                                                <option @if($item->type_operation == 'transfert') selected @endif value="transfert">Transfert</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="">Type restriction</label>
                                                                            <select class="form-control select2bs4 type" name="type_restriction" id="" style="width:100%">
                                                                                <option  value="">Selectionner un type de restriction</option>
                                                                                <option @if($item->type_restriction == 'nombre') selected @endif value="nombre">Sur le nombre</option>
                                                                                <option @if($item->type_restriction == 'montant') selected @endif value="montant">Sur le montant</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="">Valeur</label>
                                                                            <input type="text" value="{{ $item->valeur }}" class="form-control" name="valeur" placeholder="Valeur de la restriction">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="">Periodicité </label>
                                                                            <select class="form-control select2bs4 type" name="periode" id="" style="width:100%">
                                                                                <option value="">Selectionner une periodicité</option>
                                                                                <option @if($item->periode == 'definitif') selected @endif value="definitif">Sans periodicité</option>
                                                                                <option @if($item->periode == 'day') selected @endif value="day">Journalier</option>
                                                                                <option @if($item->periode == 'week') selected @endif value="week">Hebdomadaire</option>
                                                                                <option @if($item->periode == 'month') selected @endif value="month">Mensuel</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                                                            <button type="submit" class="btn btn-primary" id="add-restriction">Enregistrer</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="del-restriction{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="exampleModalLabel1">Suppression de restriction </h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <form action="/restrictions/delete/{{ $item->id }}" method="POST"> 
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Etes vous sur de supprimer cette restriction ?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                            <button type="submit" class="btn btn-primary">Oui</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @if($item->etat == 0) 
                                            <div class="modal fade" id="act-restriction{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="exampleModalLabel1">Activation de la restriction </h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>
                                                        <form action="/restrictions/activate/{{ $item->id }}" method="POST"> 
                                                            @csrf
                                                            <div class="modal-body">
                                                                <p>Etes vous sur d'activer cette restriction ?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                                <button type="submit" class="btn btn-primary">Oui</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="modal fade" id="des-restriction{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="exampleModalLabel1">Desactivation de restriction </h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>
                                                        <form action="/restrictions/desactivate/{{ $item->id }}" method="POST"> 
                                                            @csrf
                                                            <div class="modal-body">
                                                                <p>Etes vous sur de desactiver cette restriction ?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                                <button type="submit" class="btn btn-primary">Oui</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="add-gamme" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1">Definition de restriction </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="/restrictions/add" id="form-add" method="POST"> 
                        @csrf
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Type d'acteur</label>
                                            <select class="form-control select2bs4 type" name="type_acteur" id="" style="width:100%">
                                                <option value="">Selectionner un type d'acteur</option>
                                                <option value="client">Client</option>
                                                <option value="partenaire">Partenaire</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Type operation</label>
                                            <select class="form-control select2bs4 type" name="type_operation" id="" style="width:100%">
                                                <option value="">Selectionner un type d'operation</option>
                                                <option value="depot">Depot</option>
                                                <option value="retrait">Retrait</option>
                                                <option value="transfert">Transfert</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Type restriction</label>
                                            <select class="form-control select2bs4 type" name="type_restriction" id="" style="width:100%">
                                                <option value="">Selectionner un type de restriction</option>
                                                <option value="nombre">Sur le nombre</option>
                                                <option value="montant">Sur le montant</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Valeur</label>
                                            <input type="text" class="form-control" name="valeur" placeholder="Valeur de la restriction">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Periodicité </label>
                                            <select class="form-control select2bs4 type" name="periode" id="" style="width:100%">
                                                <option value="">Selectionner une periodicité</option>
                                                <option value="definitif">Sans periodicité</option>
                                                <option value="day">Journalier</option>
                                                <option value="week">Hebdomadaire</option>
                                                <option value="month">Mensuel</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary" id="add-restriction">Enregistrer</button>
                        </div>
                    </form>
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
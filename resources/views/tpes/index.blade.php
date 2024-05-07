@extends('base')
@section('css')
<link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
@endsection
@section('page')
    Liste des TPE
@endsection
@section('title')
    Liste des TPE
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <button type="button" class="btn waves-effect waves-light btn-primary" data-toggle="modal" data-target="#add-tpe">Ajouter un TPE</button>
                <br>
                <br>
                <div class="col-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Liste des TPE </h3>
                        </div>

                        <div class="box-body">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Code unique</th>
                                        <th>Patenaire</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tpes as $item)
                                        <tr>
                                            <td>{{ $item->type }}</td>
                                            <td>{{ $item->code }}</td>
                                            <td>{{ $item->partenaire_id ? $item->partenaire->libelle : 'Non défini' }}</td>
                                            <td>@if($item->status != 'on') <span class="label label-danger">Inactif</span> @else <span class="label label-success">Actif</span> @endif</td>
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
                                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#edit-tpe-{{ $item->id }}"><i class="fa fa-edit"></i>&nbsp;&nbsp;Modifier le TPE</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#del-tpe-{{ $item->id }}"><i class="fa fa-trash"></i>&nbsp;&nbsp;Supprimer le TPE</a>
                                                        </li>
                                                        <li>
                                                            @if($item->status == 'off')
                                                                <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#activation-tpe-{{ $item->id }}"><i class="fa fa-check"></i>&nbsp;&nbsp;Activer le TPE</a>
                                                            @else
                                                                <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#desactivation-tpe-{{ $item->id }}"><i class="fa fa-times"></i>&nbsp;&nbsp;Désactiver le TPE</a>
                                                            @endif
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                    <div class="modal fade" id="edit-tpe-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Modification du TPE {{ $item->code }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/tpe/edit/{{$item->id}}" method="POST"> 
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="form-group">  
                                                            <label for="recipient-name" class="control-label">Type TPE :</label>
                                                            <select required class="form-control select2bs4 type" name="type" id="type" style="width:100%">
                                                                <option value="">Selectionner un type</option>
                                                                <option @if($item->type == 'telpo') selected @endif value="telpo">Telpo</option>
                                                                <option @if($item->type == 'feitian') selected @endif value="feitian">Feitian</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">  
                                                            <label for="recipient-name" class="control-label">Code TPE :</label>
                                                            <input type="text" value="{{$item->code}}" required autocomplete="off" class="form-control" name="code">
                                                        </div>
                                                        <div class="form-group">  
                                                            <label for="recipient-name" class="control-label">Partenaire :</label>
                                                            <select required class="form-control select2bs4 type" name="partenaire" id="partenaire" style="width:100%">
                                                                <option value="">Selectionner un partenaire</option>
                                                                @foreach ($partenaires as $val)
                                                                    <option @if($item->partenaire_id == $val->id) selected @endif value="{{ $val->id }}">{{ $val->libelle }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-primary">Modifier</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="del-tpe-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Suppression de {{ $item->name.' '.$item->lastname }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/tpe/delete/{{ $item->id }}" method="POST"> 
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Etes vous sur de supprimer ce TPE?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                        <button type="submit" class="btn btn-primary">Oui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="activation-tpe-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Activation de {{ $item->lastname.' '.$item->name }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/tpe/activation/{{ $item->id }}" method="POST"> 
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Etes vous sur d'activer le compte de ce TPE?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                        <button type="submit" class="btn btn-primary">Oui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                
                                    <div class="modal fade" id="desactivation-tpe-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Desactivation du mot de passe de {{ $item->lastname.' '.$item->name }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/tpe/desactivation/{{ $item->id }}" method="POST"> 
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Etes vous sur de désactiver le compte de ce TPE?</p>
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

        <div class="modal fade" id="add-tpe" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1">Nouveau TPE</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="/tpe/add" method="POST"> 
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">  
                                <label for="recipient-name" class="control-label">Type TPE :</label>
                                <select required class="form-control select2bs4 type" name="type" id="type" style="width:100%">
                                    <option value="">Selectionner un type</option>
                                    <option value="telpo">Telpo</option>
                                    <option value="feitian">Feitian</option>
                                </select>
                            </div>
                            <div class="form-group">  
                                <label for="recipient-name" class="control-label">Code TPE :</label>
                                <input type="text" required autocomplete="off" class="form-control" name="code">
                            </div>
                            <div class="form-group">  
                                <label for="recipient-name" class="control-label">Partenaire :</label>
                                <select required class="form-control select2bs4 type" name="partenaire" id="partenaire" style="width:100%">
                                    <option value="">Selectionner un partenaire</option>
                                    @foreach ($partenaires as $item)
                                        <option value="{{ $item->id }}">{{ $item->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
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
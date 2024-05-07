@extends('base')
@section('css')
<link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endsection
@section('page')
    Liste des promotions partenaires
@endsection
@section('title')
    Liste des promotions partenaires
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn waves-effect waves-light btn-primary" data-toggle="modal" data-target="#add-promo">Ajouter une promotion </button>
                    <br>
                    <br>
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Liste des promotions </h3>
                        </div>

                        <div class="box-body">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Operation</th>
                                        <th>Date debut</th>
                                        <th>Date fin</th>
                                        <th>Type promo partenaire</th>
                                        <th>Nature gain partenaire</th>
                                        <th>Gain partenaire</th>
                                        <th>Type promo client</th>
                                        <th>Nature gain client</th>
                                        <th>Gain client</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($promotions as $item)
                                        <tr>
                                            <td>{{ $item->operation }}</td>
                                            <td>{{ $item->date_debut }}</td>
                                            <td>{{ $item->date_fin }}</td>
                                            <td>{{ $item->type_promo_partenaire }}</td>
                                            <td>{{ $item->type_gain_partenaire }}</td>
                                            <td>{{ $item->gain_partenaire }}</td>
                                            <td>{{ $item->type_promo_client }}</td>
                                            <td>{{ $item->type_gain_client }}</td>
                                            <td>{{ $item->gain_client }}</td>
                                            <td>{{ $item->status }}</td>
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
                                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#edit-promo-{{ $item->id }}"><i class="fa fa-edit"></i>&nbsp;&nbsp;Modifier</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#del-promo-{{ $item->id }}"><i class="fa fa-trash"></i>&nbsp;&nbsp;Supprimer</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="edit-promo-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="exampleModalLabel1">Modification de {{ $item->name.' '.$item->lastname }}</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <form action="/user/edit/{{ $item->id }}" method="POST"> 
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="">Type d'operation :</label>
                                                                <select required class="form-control select2bs4 type" name="operation" id="" style="width:100%">
                                                                    <option value="">Selectionner un type</option>
                                                                    <option @if($item->operation == 'buy_carte') selected @endif value="buy_carte"> Achat de carte</option>
                                                                    <option @if($item->operation == 'add_depot') selected @endif value="add_depot">Depot client</option>
                                                                    <option @if($item->operation == 'rec_account') selected @endif value="rec_account">Rechargement de compte</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">  
                                                                <label for="recipient-name" class="control-label">Debut :</label>
                                                                <input type="date" value="{{$item->date_debut}}" required autocomplete="off" class="form-control" name="date_debut">
                                                            </div>
                                                            <div class="form-group">  
                                                                <label for="recipient-name" class="control-label">Fin :</label>
                                                                <input type="date" value="{{$item->date_fin}}" required autocomplete="off" class="form-control" name="date_fin">
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="">Type de promo client :</label>
                                                                        <select required class="form-control select2bs4 type" name="type_promo_client" id="" style="width:100%">
                                                                            <option value="">Selectionner un type</option>
                                                                            <option @if($item->type_promo_client == 'reduction') selected @endif value="reduction">Reduction</option>
                                                                            <option @if($item->type_promo_client == 'bonus') selected @endif value="bonus">Bonus</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="">Nature gain client :</label>
                                                                        <select required class="form-control select2bs4 type" name="type_gain_client" id="" style="width:100%">
                                                                            <option value="">Selectionner une nature</option>
                                                                            <option @if($item->type_gain_client == 'fixe') selected @endif value="fixe">Fixe</option>
                                                                            <option @if($item->type_gain_client == 'pourcentage') selected @endif value="pourcentage">Pourcentage</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">  
                                                                        <label for="recipient-name" class="control-label">Valeur gain client :</label>
                                                                        <input type="number" value="{{$item->gain_client}}" required autocomplete="off" class="form-control" name="gain_client">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="">Type de promo partenaire :</label>
                                                                        <select required class="form-control select2bs4 type" name="type_promo_partenaire" id="" style="width:100%">
                                                                            <option value="">Selectionner un type</option>
                                                                            <option @if($item->type_promo_partenaire == 'reduction') selected @endif value="reduction">Reduction</option>
                                                                            <option @if($item->type_promo_partenaire == 'reduction') selected @endif value="bonus">Bonus</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="">Nature gain partenaire :</label>
                                                                        <select required class="form-control select2bs4 type" name="type_gain_partenaire" id="" style="width:100%">
                                                                            <option value="">Selectionner une nature</option>
                                                                            <option @if($item->type_gain_partenaire == 'fixe') selected @endif value="fixe">Fixe</option>
                                                                            <option @if($item->type_gain_partenaire == 'pourcentage') selected @endif value="pourcentage">Pourcentage</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">  
                                                                        <label for="recipient-name" class="control-label">Valeur gain partenaire :</label>
                                                                        <input type="number" value="{{$item->gain_partenaire}}" required autocomplete="off" class="form-control" name="gain_partenaire">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
    
                                        <div class="modal fade" id="del-promo-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="exampleModalLabel1">Suppression de {{ $item->name.' '.$item->lastname }}</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <form action="/user/delete/{{ $item->id }}" method="POST"> 
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Etes vous sur de supprimer cet utilisateur?</p>
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
        <div class="modal fade" id="add-promo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="/promotion/partenaire/add" method="POST"> 
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="">Type d'operation :</label>
                                <select required class="form-control select2bs4 type" name="operation" id="" style="width:100%">
                                    <option value="">Selectionner un type</option>
                                    <option value="buy_carte"> Achat de carte</option>
                                    <option value="add_depot">Depot client</option>
                                    <option value="rec_account">Rechargement de compte</option>
                                </select>
                            </div>
                            <div class="form-group">  
                                <label for="recipient-name" class="control-label">Debut :</label>
                                <input type="date" required autocomplete="off" class="form-control" name="date_debut">
                            </div>
                            <div class="form-group">  
                                <label for="recipient-name" class="control-label">Fin :</label>
                                <input type="date" required autocomplete="off" class="form-control" name="date_fin">
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Type de promo client :</label>
                                        <select required class="form-control select2bs4 type" name="type_promo_client" id="" style="width:100%">
                                            <option value="">Selectionner un type</option>
                                            <option value="reduction">Reduction</option>
                                            <option value="bonus">Bonus</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Nature gain client :</label>
                                        <select required class="form-control select2bs4 type" name="type_gain_client" id="" style="width:100%">
                                            <option value="">Selectionner une nature</option>
                                            <option value="fixe">Fixe</option>
                                            <option value="pourcentage">Pourcentage</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">  
                                        <label for="recipient-name" class="control-label">Valeur gain client :</label>
                                        <input type="number" required autocomplete="off" class="form-control" name="gain_client">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Type de promo partenaire :</label>
                                        <select required class="form-control select2bs4 type" name="type_promo_partenaire" id="" style="width:100%">
                                            <option value="">Selectionner un type</option>
                                            <option value="reduction">Reduction</option>
                                            <option value="bonus">Bonus</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Nature gain partenaire :</label>
                                        <select required class="form-control select2bs4 type" name="type_gain_partenaire" id="" style="width:100%">
                                            <option value="">Selectionner une nature</option>
                                            <option value="fixe">Fixe</option>
                                            <option value="pourcentage">Pourcentage</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">  
                                        <label for="recipient-name" class="control-label">Valeur gain partenaire :</label>
                                        <input type="number" required autocomplete="off" class="form-control" name="gain_partenaire">
                                    </div>
                                </div>
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
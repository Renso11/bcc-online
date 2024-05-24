@extends('base')
@section('css')
    <link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endsection
@section('page')
    Liste des apporteurs
@endsection
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <button type="button" class="btn waves-effect waves-light btn-primary" data-toggle="modal" data-target="#add-apporteur">Ajouter un apporteur</button>
                <br>
                <br>
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Liste des apporteur </h3>
                    </div>

                    <div class="box-body">
                        <table class="table table-bordered table-striped example1">
                            <thead>
                                <tr>
                                    <th>Nom et prenoms</th>
                                    <th>Telephone</th>
                                    <th>Code promo</th>
                                    <th>Solde</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($apporteurs as $item)
                                    <tr>
                                        <td>{{ $item->name.' '.$item->lastname }}</td>
                                        <td>+{{ $item->telephone }}</td>
                                        <td>{{ $item->code_promo }}</td>
                                        <td>{{ $item->solde_commission }}</td>
                                        <td>@if($item->status == 0) <span class="label label-danger">Inactif</span> @else <span class="label label-success">Actif</span> @endif</td>
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
                                                        <a class="dropdown-item" href="/apporteur/operations/{{$item->id}}"><i class="fa fa-money-o"></i>&nbsp;&nbsp;Operations</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#reg-code-promo-{{ $item->id }}"><i class="fa fa-spinner"></i>&nbsp;&nbsp;Regenerer le code promo</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#reset-password-apporteur-{{ $item->id }}"><i class="fa fa-spinner"></i>&nbsp;&nbsp;Reinitialiser le mot de passe</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#edit-apporteur-{{ $item->id }}"><i class="fa fa-edit"></i>&nbsp;&nbsp;Modifier les informations</a>
                                                    </li>
                                                    <li>
                                                        @if($item->status == 0)
                                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#activation-apporteur-{{ $item->id }}"><i class="fa fa-check"></i>&nbsp;&nbsp;Activer l'apporteur</a>
                                                        @else
                                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#desactivation-apporteur-{{ $item->id }}"><i class="fa fa-times"></i>&nbsp;&nbsp;Désactiver l'apporteur</a>
                                                        @endif
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#del-apporteur-{{ $item->id }}"><i class="fa fa-trash"></i>&nbsp;&nbsp;Supprimer l'apporteur</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="edit-apporteur-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Modification de {{ $item->name.' '.$item->lastname }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/apporteur/edit/{{ $item->id }}" method="POST"> 
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="form-group">  
                                                            <label for="recipient-name" class="control-label">Nom :</label>
                                                            <input type="text" value="{{ $item->name }}" autocomplete="off" class="form-control" name="name">
                                                        </div>
                                                        <div class="form-group">  
                                                            <label for="recipient-name" class="control-label">Prenom :</label>
                                                            <input type="text" value="{{ $item->lastname }}" autocomplete="off" class="form-control" name="lastname">
                                                        </div>
                                                        <div class="form-group">  
                                                            <label for="recipient-name" class="control-label">Telephone :</label>
                                                            <input type="text" value="{{ $item->telephone }}" autocomplete="off" class="form-control" name="telephone">
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

                                    

                                    <div class="modal fade" id="del-apporteur-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Suppression de {{ $item->name.' '.$item->lastname }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/apporteur/delete/{{ $item->id }}" method="POST"> 
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Etes vous sur de supprimer cet apporteur?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                        <button type="submit" class="btn btn-primary">Oui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="activation-apporteur-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Activation de {{ $item->lastname.' '.$item->name }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/apporteur/activate/{{ $item->id }}" method="POST"> 
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Etes vous sur d'activer le compte de cet apporteur?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                        <button type="submit" class="btn btn-primary">Oui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                
                                    <div class="modal fade" id="desactivation-apporteur-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Desactivation du mot de passe de {{ $item->lastname.' '.$item->name }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/apporteur/desactivate/{{ $item->id }}" method="POST"> 
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Etes vous sur de désactiver le compte de cet apporteur?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                        <button type="submit" class="btn btn-primary">Oui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="reset-password-apporteur-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Reinitialisation du mot de passe de {{ $item->lastname.' '.$item->name }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/apporteur/reset/password/{{ $item->id }}" method="POST"> 
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Etes vous sur de réinitialiser le mot de passe de cet apporteur?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                        <button type="submit" class="btn btn-primary">Oui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="reg-code-promo-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Regeneration du mot du code promo de {{ $item->lastname.' '.$item->name }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/apporteur/reset/code/{{ $item->id }}" method="POST"> 
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Etes vous sur de réinitialiser le mot de passe de cet apporteur?</p>
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

    <div class="modal fade" id="add-apporteur" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel1">Nouvel apporteur</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="/apporteur/add" method="POST"> 
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">  
                            <label for="recipient-name" class="control-label">Nom :</label>
                            <input type="text" required autocomplete="off" class="form-control" name="name">
                        </div>
                        <div class="form-group">  
                            <label for="recipient-name" class="control-label">Prénom :</label>
                            <input type="text" required autocomplete="off" class="form-control" name="lastname">
                        </div>
                        <div class="form-group">  
                            <label for="recipient-name" class="control-label">Telephone :</label>
                            <input type="text" required autocomplete="off" class="form-control" name="telephone">
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
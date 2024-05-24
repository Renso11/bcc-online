@extends('base')
@section('css')
<link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
@endsection
@section('page')
    Liste des comptes clients en attentes
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Liste des comptes clients en attentes </h3>
                        </div>
    
                        <div class="box-body">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Date de création</th>
                                        <th>Nom et prénoms</th>
                                        <th>Telephone</th>
                                        <th>Status</th>
                                        <th>Verification</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userClients as $item)
                                        <tr>
                                            <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                            <td>{{ $item->lastname.' '.$item->name }}</td>
                                            <td>{{ $item->username }}</td>
                                            <td>@if($item->status == 0) <span class="label label-danger">Inactif</span> @else <span class="label label-success">Actif</span> @endif</td>
                                            <td>@if($item->verification == 0) <span class="label label-danger">Non vérifié</span> @else <span class="label label-success">Vérifié</span> @endif</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default btn-flat">Actions</button>
                                                    <button type="button" class="btn btn-default btn-flat dropdown-toggle"
                                                        data-toggle="dropdown">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        @if (hasPermission('admin.client.validation'))
                                                            @if ($item->kyc_client_id)
                                                                <li>
                                                                    <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#validation-client-{{ $item->id }}"><i class="fa fa-check"></i>&nbsp;&nbsp;Valider / Rejeter </a>
                                                                </li>
                                                            @endif
                                                        @endif
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#reset-password-client-{{ $item->id }}"><i class="fa fa-spinner"></i>&nbsp;&nbsp;Reinitialisater le mot de passe</a>
                                                        </li>
                                                        @if (hasPermission('admin.client.activation') || hasPermission('admin.client.desactivation'))
                                                            @if($item->status == 0)
                                                                <li>
                                                                    <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#activation-client-{{ $item->id }}"><i class="fa fa-check"></i>&nbsp;&nbsp;Activer le compte</a>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#desactivation-client-{{ $item->id }}"><i class="fa fa-times"></i>&nbsp;&nbsp;Désactiver le compte</a>
                                                                </li>
                                                            @endif
                                                        @endif
                                                        @if (hasPermission('admin.client.delete'))
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#del-client-{{ $item->id }}"><i class="fa fa-trash"></i>&nbsp;&nbsp;Supprimer le compte</a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="del-client-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                            <div class="modal-dialog modal-sm" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="exampleModalLabel1">Suppression de {{ $item->lastname.' '.$item->name }}</h4>
                                                    </div>
                                                    <form action="/client/delete/{{ $item->id }}" method="POST"> 
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Etes vous sur de supprimer ce client?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                            <button type="submit" class="btn btn-primary">Oui</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="reset-password-client-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                            <div class="modal-dialog modal-sm" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="exampleModalLabel1">Reinitialisation du mot de passe de {{ $item->lastname.' '.$item->name }}</h4>
                                                    </div>
                                                    <form action="/client/reset/password/{{ $item->id }}" method="POST"> 
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Etes vous sur de réinitialiser le mot de passe de client?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                            <button type="submit" class="btn btn-primary">Oui</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="activation-client-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                            <div class="modal-dialog modal-sm" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="exampleModalLabel1">Activation de {{ $item->lastname.' '.$item->name }}</h4>
                                                    </div>
                                                    <form action="/client/activation/{{ $item->id }}" method="POST"> 
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Etes vous sur d'activer le compte de ce client?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                            <button type="submit" class="btn btn-primary">Oui</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="desactivation-client-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                            <div class="modal-dialog modal-sm" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="exampleModalLabel1">Désactivation de {{ $item->lastname.' '.$item->name }}</h4>
                                                    </div>
                                                    <form action="/client/desactivation/{{ $item->id }}" method="POST"> 
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Etes vous sur de désactiver le compte de ce client?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                            <button type="submit" class="btn btn-primary">Oui</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="valid-conf-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                            <div class="modal-dialog modal-sm" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel1">Validation du dossier de {{ $item->lastname.' '.$item->name }}</h5>
                                                    </div>
                                                    <form action="/client/validation/{{ $item->id }}" method="POST"> 
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Etes vous sur de valider le compte de ce client?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                            <button type="submit" class="btn btn-primary">Oui</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="rejet-conf-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                            <div class="modal-dialog modal-sm" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel1">Rejet du dossier de {{ $item->lastname.' '.$item->name }}</h5>
                                                    </div>
                                                    <form action="/client/rejet/{{ $item->id }}" method="POST"> 
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="recipient-name" class="control-label">Niveau du rejet</label>
                                                                <select class="form-control select2bs4" required name="niveau" id="niveau"  data-placeholder="Selectionner le niveau">
                                                                    <option value="">Selectionner le motif du rejet</option>
                                                                    <option value="2">Information incorrecte</option>
                                                                    <option value="3">Pieces ou photo non valide</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="recipient-name" class="control-label">Description:</label>
                                                                <textarea class="form-control" name="description" id="" rows="5"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                            <button type="submit" class="btn btn-primary">Oui</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        @if($item->kyc_client_id)
                                            <div class="modal fade" id="validation-client-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="exampleModalLabel1">KYC de {{ $item->kycClient->lastname.' '.$item->kycClient->name }} | Demande du {{ $item->updated_at->format('d-m-Y H:i:s') }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>
                                                        <form action="/client/edit/{{ $item->id }}" method="POST"> 
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <label for="">Nom et prénoms</label>
                                                                        <p>{{ $item->kycClient->name.' '.$item->kycClient->lastname }}</p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Email</label>
                                                                        <p>{{ $item->kycClient->email }}</p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Telephone</label>
                                                                        <p>{{ $item->kycClient->telephone }}</p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Naissance</label>
                                                                        <p>{{ $item->kycClient->birthday }}</p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Departement</label>
                                                                        <p class="text-capitalize">{{ $item->kycClient->departement }}</p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Ville</label>
                                                                        <p>{{ $item->kycClient->city }}</p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Adresse</label>
                                                                        <p>{{ $item->kycClient->address }}</p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Type de piece</label>
                                                                        <p>@if($item->kycClient->piece_type == 1) Passeport @elseif($item->piece == 2) CNI @elseif($item->piece == 3) Permis de conduire @else Autres @endif</p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Numero de la piece</label>    
                                                                        <p>{{ $item->kycClient->piece_id }}</p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Piece du client</label> <br>
                                                                        <a href="{{ $item->kycClient->piece_file }}" target="_blank">
                                                                            <img src="{{ $item->kycClient->piece_file }}" alt="" srcset="" width="50%">
                                                                        </a>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Client avec la piece</label> <br>
                                                                        <a href="{{ $item->kycClient->user_with_piece }}" target="_blank">
                                                                            <img src="{{ $item->kycClient->user_with_piece }}" alt="" srcset="" width="50%">
                                                                        </a>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Signature du client</label> <br>
                                                                        <a href="{{ $item->kycClient->signature }}" target="_blank" class="btn btn-primary">
                                                                            <img src="{{ $item->kycClient->signature }}" alt="" srcset="" width="50%">
                                                                        </a>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Verification numero</label>
                                                                        <p>@if($item->verification_step_one == 0) <span class="label label-danger">Non vérifié</span> @else <span class="label label-success">Vérifié</span> @endif</p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Verification informations</label>
                                                                        <p>@if($item->verification_step_two == 0) <span class="label label-danger">Non vérifié</span> @else <span class="label label-success">Vérifié</span> @endif</p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="">Verification identité</label>
                                                                        <p>@if($item->verification_step_three == 0) <span class="label label-danger">Non vérifié</span> @else <span class="label label-success">Vérifié</span> @endif</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                @if($item->verification_step_one == 1 && $item->verification_step_two == 1 && $item->verification_step_three == 1 && $item->verification == 0)
                                                                    <button type="button" data-dismiss="modal" data-toggle="modal" data-target="#valid-conf-{{ $item->id }}" class="btn btn-success">Valider le compte</button>
                                                                @endif
                                                                <button data-dismiss="modal" data-toggle="modal" data-target="#rejet-conf-{{ $item->id }}" class="btn btn-primary">Rejeter le KYC</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
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
    <script>
        function formatState (state) {
            if (!state.id) { 
                return state.text; 
            }
            var $state = $(
                '<span><img src="/assets/images/flags/' +  state.element.dataset.flag.toLowerCase() +
                '.svg" class="img-flag" /> ' +
                state.text +  '</span>'
            );
            return $state;
        };

        $(".select2-flag-search").select2({
            templateResult: formatState,
            templateSelection: formatState,
            escapeMarkup: function(m) { return m; },
            width: '100%',
            dropdownParent: $("#add-client")
        });
        
    </script>
@endsection
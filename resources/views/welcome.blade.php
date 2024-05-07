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
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>{{ $nbClients }}</h3>
                        <p>Nombre de compte clients</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="#" class="small-box-footer">Détails <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{ $nbPartenaires }}</h3>
                        <p>Nombre de compte partenaires</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="#" class="small-box-footer">Détails <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ $solde['gtp'] }} <sup style="font-size: 20px">XOF</sup> </h3>
                        <p>Solde GTP</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="#" class="small-box-footer">Détails <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{ $solde['bmo_debit'] }} <sup style="font-size: 20px">XOF</sup></h3>
                        <p>Solde BCV Cash Collect (Debit)</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="#" class="small-box-footer">Détails <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{ $solde['bmo_credit'] }} <sup style="font-size: 20px">XOF</sup></h3>
                        <p>Solde Virtual Load (Credit)</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="#" class="small-box-footer">Détails <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ $solde['kkiapay'] }} <sup style="font-size: 20px">XOF</sup> </h3>
                        <p>Solde Kkiapay</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="#" class="small-box-footer">Recharger le compte <i class="fa fa-plus"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{ $solde['compte_partenaire'] }} <sup style="font-size: 20px">XOF</sup> </h3>
                        <p>Solde total compte partenaire</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="#" class="small-box-footer">Détails <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">5 derniers compte clients en attente de validation </h3>
                    </div>

                    <div class="box-body">
                        <table class="table table-bordered table-striped example1">
                            <thead>
                                <tr>
                                    <th>Nom et prénoms</th>
                                    <th>Telephone</th>
                                    <th>Status</th>
                                    <th>Verification</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($comptesClientsEnAttentes as $item)
                                    <tr>
                                        <td>{{ $item->lastname . ' ' . $item->name }}</td>
                                        <td>{{ $item->username }}</td>
                                        <td>
                                            @if ($item->status == 0)
                                                <span class="label label-danger">Inactif</span>
                                            @else
                                                <span class="label label-success">Actif</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->verification == 0)
                                                <span class="label label-danger">Non vérifié</span>
                                            @else
                                                <span class="label label-success">Vérifié</span>
                                            @endif
                                        </td>
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
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" @if($item->type == 'admin') data-target="#edit-role-{{ $item->id }}" @else data-target="#edit-role-{{ $item->id }}" @endif><i class="fa fa-edit"></i>&nbsp;&nbsp;Modifier les informations</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#del-role-{{ $item->id }}"><i class="fa fa-trash"></i>&nbsp;&nbsp;Supprimer le role</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="edit-client-{{ $item->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Modification de
                                                        {{ $item->lastname . ' ' . $item->name }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/client/edit/{{ $item->id }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="recipient-name" class="control-label">Nom:</label>
                                                            <input type="text" value="{{ $item->name }}"
                                                                autocomplete="off" class="form-control" name="name">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="message-text"
                                                                class="control-label">Prenom:</label>
                                                            <input type="text" value="{{ $item->lastname }}"
                                                                autocomplete="off" class="form-control" name="lastname">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="recipient-name"
                                                                class="control-label">Code:</label>
                                                            <input type="text" value="{{ $item->code }}"
                                                                autocomplete="off" class="form-control" name="code">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="message-text" class="control-label">4 dernier
                                                                chiffres:</label>
                                                            <input type="text" value="{{ $item->last }}"
                                                                autocomplete="off" class="form-control" name="last">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                            data-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-primary">Modifier</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="del-client-{{ $item->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Suppression de
                                                        {{ $item->lastname . ' ' . $item->name }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/client/delete/{{ $item->id }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Etes vous sur de supprimer ce client?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                            data-dismiss="modal">Non</button>
                                                        <button type="submit" class="btn btn-primary">Oui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="reset-password-client-{{ $item->id }}"
                                        tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Reinitialisation
                                                        du mot de passe de {{ $item->lastname . ' ' . $item->name }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/client/reset/password/{{ $item->id }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Etes vous sur de réinitialiser le mot de passe de client?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                            data-dismiss="modal">Non</button>
                                                        <button type="submit" class="btn btn-primary">Oui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="activation-client-{{ $item->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Activation de
                                                        {{ $item->lastname . ' ' . $item->name }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/client/activation/{{ $item->id }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Etes vous sur d'activer le compte de ce client?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                            data-dismiss="modal">Non</button>
                                                        <button type="submit" class="btn btn-primary">Oui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="desactivation-client-{{ $item->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Désactivation de
                                                        {{ $item->lastname . ' ' . $item->name }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/client/desactivation/{{ $item->id }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Etes vous sur de désactiver le compte de ce client?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                            data-dismiss="modal">Non</button>
                                                        <button type="submit" class="btn btn-primary">Oui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="valid-conf-{{ $item->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel1">Validation du
                                                        dossier de {{ $item->lastname . ' ' . $item->name }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/client/validation/{{ $item->id }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Etes vous sur de valider le compte de ce client?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                            data-dismiss="modal">Non</button>
                                                        <button type="submit" class="btn btn-primary">Oui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="rejet-conf-{{ $item->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel1">Rejet du dossier
                                                        de {{ $item->lastname . ' ' . $item->name }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/client/rejet/{{ $item->id }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="recipient-name" class="control-label">Niveau
                                                                du rejet</label>
                                                            <select class="form-control select2bs4" required
                                                                name="niveau" id="niveau"
                                                                data-placeholder="Selectionner le niveau">
                                                                <option value="">Selectionner le motif du rejet
                                                                </option>
                                                                <option value="2">Information incorrecte</option>
                                                                <option value="3">Pieces ou photo non valide
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="recipient-name"
                                                                class="control-label">Description:</label>
                                                            <textarea class="form-control" name="description" id="" rows="5"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                            data-dismiss="modal">Non</button>
                                                        <button type="submit" class="btn btn-primary">Oui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($item->kyc_client_id)
                                        <div class="modal fade" id="validation-client-{{ $item->id }}"
                                            tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="exampleModalLabel1">KYC de
                                                            {{ $item->kycClient->lastname . ' ' . $item->kycClient->name }}
                                                            | Demande du {{ $item->updated_at->format('d-m-Y H:i:s') }}</h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close"><span
                                                                aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <form action="/client/edit/{{ $item->id }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <label for="">Nom et prénoms</label>
                                                                    <p>{{ $item->kycClient->name . ' ' . $item->kycClient->lastname }}
                                                                    </p>
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
                                                                    <p class="text-capitalize">
                                                                        {{ $item->kycClient->departement }}</p>
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
                                                                    <p>
                                                                        @if ($item->kycClient->piece_type == 1)
                                                                            Passeport
                                                                        @elseif($item->piece == 2)
                                                                            CNI
                                                                        @elseif($item->piece == 3)
                                                                            Permis de conduire
                                                                        @else
                                                                            Autres
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="">Numero de la piece</label>
                                                                    <p>{{ $item->kycClient->piece_id }}</p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="">Piece du client</label> <br>
                                                                    <a href="{{ $item->kycClient->piece_file }}"
                                                                        target="_blank" class="btn btn-primary">
                                                                        Voir la piece
                                                                    </a>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="">Client avec la piece</label>
                                                                    <br>
                                                                    <a href="{{ $item->kycClient->user_with_piece }}"
                                                                        target="_blank" class="btn btn-primary">
                                                                        Voir le client avec la piece
                                                                    </a>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="">Verification numero</label>
                                                                    <p>
                                                                        @if ($item->verification_step_one == 0)
                                                                            <span class="label label-danger">Non
                                                                                vérifié</span>
                                                                        @else
                                                                            <span
                                                                                class="label label-success">Vérifié</span>
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="">Verification
                                                                        informations</label>
                                                                    <p>
                                                                        @if ($item->verification_step_two == 0)
                                                                            <span class="label label-danger">Non
                                                                                vérifié</span>
                                                                        @else
                                                                            <span
                                                                                class="label label-success">Vérifié</span>
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="">Verification identité</label>
                                                                    <p>
                                                                        @if ($item->verification_step_three == 0)
                                                                            <span class="label label-danger">Non
                                                                                vérifié</span>
                                                                        @else
                                                                            <span
                                                                                class="label label-success">Vérifié</span>
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            @if (
                                                                $item->verification_step_one == 1 &&
                                                                    $item->verification_step_two == 1 &&
                                                                    $item->verification_step_three == 1 &&
                                                                    $item->verification == 0)
                                                                <button type="button" data-dismiss="modal"
                                                                    data-toggle="modal"
                                                                    data-target="#valid-conf-{{ $item->id }}"
                                                                    class="btn btn-success">Valider le compte</button>
                                                            @endif
                                                            <button data-dismiss="modal" data-toggle="modal"
                                                                data-target="#rejet-conf-{{ $item->id }}"
                                                                class="btn btn-primary">Rejeter le KYC</button>
                                                            <button type="button" class="btn btn-danger"
                                                                data-dismiss="modal">Annuler</button>
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
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">5 dernieres operations clients en attentes </h3>
                    </div>

                    <div class="box-body">
                        <table class="table table-bordered table-striped example1">
                            <thead>
                                <tr>
                                    <th style="width:15%">Date</th>
                                    <th style="width:15%">Client</th>
                                    <th style="width:10%">Type</th>
                                    <th style="width:10%">Moyen</th>
                                    <th style="width:10%">Montant</th>
                                    <th style="width:10%">Frais</th>
                                    <th style="width:10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($operationsClientsEnAttentes as $item)
                                    <tr>
                                        <td>{{ $item['date'] }}</td>
                                        <td>{{ $item['userClient']->name . ' ' . $item['userClient']->lastname }}</td>
                                        <td>{{ $item['type'] }}</td>
                                        <td>{{ $item['moyen_paiement'] }}</td>
                                        <td>{{ $item['montant'] }}</td>
                                        <td>{{ $item['frais'] }}</td>
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
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#show-transaction-{{ $item['id'] }}"><i class="fa fa-eye"></i>&nbsp;&nbsp;Voir plus</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#refund-transaction-{{ $item['id'] }}"><i class="fa fa-spinner"></i>&nbsp;&nbsp;Rembourser la transaction</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#complete-transaction-{{ $item['id'] }}"><i class="fa fa-check"></i>&nbsp;&nbsp;Finaliser la transaction</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#cancel-transaction-{{ $item['id'] }}"><i class="fa fa-times"></i>&nbsp;&nbsp;Annuler la transaction</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

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
                                                        @if($item['type'] == 'Recharge')
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
                                                <form action="/client/operations/attentes/complete" method="POST">
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
                                    
                                    <div class="modal fade" id="refund-transaction-{{ $item['id'] }}" tabindex="-1" role="dialog"
                                        aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Remboursement de la transaction</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/client/operations/attentes/refund" method="POST">
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
                                                <form action="/client/operations/attentes/refund" method="POST">
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">5 dernieres opérations partenaires en attentes </h3>
                    </div>

                    <div class="box-body">
                        <table class="table table-bordered table-striped example1">
                            <thead>
                                <tr>
                                    <th style="width:15%">Date</th>
                                    <th style="width:15%">Client</th>
                                    <th style="width:10%">Type</th>
                                    <th style="width:10%">Montant</th>
                                    <th style="width:10%">Frais</th>
                                    <th style="width:10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($operationsPartenairesEnAttentes as $item)
                                    <tr>
                                        <td>{{ $item['date'] }}</td>
                                        <td>{{ $item['userClient']->name . ' ' . $item['userClient']->lastname }}</td>
                                        <td>{{ $item['type'] }}</td>
                                        <td>{{ $item['montant'] }}</td>
                                        <td>{{ $item['frais'] }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="javascript:void(0)"
                                                        data-toggle="modal"
                                                        data-target="#validation-client-{{ $item['id'] }}"><i
                                                            class="fa fa-eye"></i>&nbsp;&nbsp;Voir plus</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="add-partenaire" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel1">Rechargement du compte Kkiapay</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <form action="/recharge/kkp" method="POST" id="form-recharge-kkp" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">Montant du rechargement</label>
                            <input type="number" autocomplete="off" required class="form-control" name="montant"
                                    id="montant">
                                <input type="hidden" autocomplete="off" required name="reference" id="reference">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                        <button type="button" id="pay" class="btn btn-primary">Recharger</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

    <script src="https://cdn.kkiapay.me/k.js"></script>
    <script>
        $(".example1").DataTable({
            ordering: false
        });

        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

        $('#pay').on('click', function(e) {
            e.preventDefault()

            var montant = $("#montant").val();
            openKkiapayWidget({
                amount: montant,
                position: "center",
                sandbox: "false",
                theme: "#975102",
                key: "653a4b85df3c403ad1fb39a64cc9a9ef874432db"
            })
            addSuccessListener(response => {
                $('#reference').val(response.transactionId)
                $('#form-recharge-kkp').submit();
            });
        })
    </script>
@endsection

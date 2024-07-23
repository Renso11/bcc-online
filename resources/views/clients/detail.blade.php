@extends('base')
@section('css')
    <link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="/plugins/intl-tel-input-master/build/css/intlTelInput.css" />
    <link rel="stylesheet" href="/plugins/intl-tel-input-master/build/css/demo.css" />
@endsection
@section('title')
    Détails du client
@endsection
@section('page')
    Détails du client
@endsection
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-3">

                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <h3 class="profile-username text-center">{{ $userClient->name . ' ' . $userClient->lastname }}</h3>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>Téléphone</b> <a class="pull-right">{{ '+'.$userClient->username }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Verification 1</b>
                                <div class="pull-right">
                                    @if ($userClient->verification_step_one == 1)
                                        <i class="fa fa-check" style="color: green"></i>
                                    @else
                                        <i class="fa fa-times" style="color: green"></i>
                                    @endif
                                </div>
                            </li>
                            <li class="list-group-item">
                                <b>Verification 2</b>
                                <div class="pull-right">
                                    @if ($userClient->verification_step_two == 1)
                                        <i class="fa fa-check" style="color: green"></i>
                                    @else
                                        <i class="fa fa-times" style="color: green"></i>
                                    @endif
                                </div>
                            </li>
                            <li class="list-group-item">
                                <b>Verification 3</b> 
                                <div class="pull-right">
                                    @if ($userClient->verification_step_three == 1)
                                        <i class="fa fa-check" style="color: green"></i>
                                    @else
                                        <i class="fa fa-times" style="color: green"></i>
                                    @endif
                                </div>
                            </li>
                            <li class="list-group-item">
                                <b>Derniere connexion</b> <a class="pull-right">{{ $userClient->last_connexion }}</a>
                            </li>
                        </ul>
                        <button type="button" data-toggle="modal" data-target="#del-client-{{ $userClient->id }}"
                            class="btn btn-danger"><i class="fa fa-trash"></i></button>
                        <button data-toggle="modal" data-target="#reset-password-client-{{ $userClient->id }}"
                            type="button"class="btn btn-default"><i class="fa fa-spinner"></i></button>
                        @if ($userClient->status == 0)
                            <button type="button" data-toggle="modal" data-target="#activation-client-{{ $userClient->id }}" class="btn btn-success">
                                <i class="fa fa-check"></i>
                            </button>
                        @else
                            <button type="button" data-toggle="modal" data-target="#desactivation-client-{{ $userClient->id }}" class="btn btn-warning">
                                <i class="fa fa-times"></i>
                            </button>
                        @endif                                  
                        <button type="button" data-toggle="modal" data-target="#print-releve-client" class="btn btn-primary">
                            <i class="fa fa-print"></i>
                        </button>
                    </div>
                </div>

            </div>

            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#card" data-toggle="tab">Cartes</a></li>
                        <li><a href="#rec" data-toggle="tab">Rechargements</a></li>
                        <li><a href="#trf" data-toggle="tab">Transferts</a></li>
                        <li><a href="#dep" data-toggle="tab">Depots</a></li>
                        <li><a href="#ret" data-toggle="tab">Retraits</a></li>
                        <li><a href="#kyc" data-toggle="tab">KYC</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="card">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Date d'ajout</th>
                                        <th>Customer ID</th>
                                        <th>Last Digits</th>
                                        <th>Web Code</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cards as $item)
                                        <tr>
                                            <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                            <td>{{ decryptData((string)$item->customer_id,env('ENCRYPT_KEY')) }}</td>
                                            <td>{{ decryptData((string)$item->last_digits,env('ENCRYPT_KEY')) }}</td>
                                            <td>{{ decryptData((string)$item->pass_code,env('ENCRYPT_KEY')) }}</td>
                                            <td>
                                                @if ($item->is_first == 1) 
                                                    <span class="label label-success">Principale</span>
                                                @else
                                                    <span class="label label-default">Secondaire</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-danger"><i class="fa fa-minus"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane" id="rec">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Moyen de paiement</th>
                                        <th>Montant</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recharges as $item)
                                        <tr>
                                            <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                            <td>{{ $item->moyen_paiement }}</td>
                                            <td>{{ $item->montant }} F CFA</td>
                                            <td>
                                                <span class="label label-success">Effectue</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane" id="trf">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Receveur</th>
                                        <th>Moyen de paiement</th>
                                        <th>Montant</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transferts as $item)
                                        <tr>           
                                            <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>     
                                            <td>
                                                @if($item->moyen_paiement == 'bmo')
                                                    {{ '+'.$item->receveur_telephone }} 
                                                @elseif($item->moyen_paiement == 'momo')
                                                    {{ '+'.$item->receveur_telephone }} 
                                                @elseif($item->moyen_paiement == 'card')
                                                    {{ $item->receveur_customer_id.','.$item->receveur_last_digits }} 
                                                @else
                                                    {{ $item->receveur->name . ' ' . $item->receveur->lastname }} 
                                                @endif
                                            </td>         
                                            <td>{{ $item->moyen_paiement }}</td>   
                                            <td>{{ $item->montant }} F CFA</td>
                                            <td>
                                                <span class="label label-success">Effectue</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane" id="dep">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Partenaire</th>
                                        <th>Montant</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($depots as $item)
                                        <tr>
                                            <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td> 
                                            <td>{{ $item->partenaire->libelle }}</td>
                                            <td>{{ $item->montant }} F CFA</td>
                                            <td>
                                                <span class="label label-success">Effectue</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane" id="ret">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Partenaire</th>
                                        <th>Montant</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($retraits as $item)
                                        <tr>
                                            <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td> 
                                            <td>{{ $item->partenaire->libelle }}</td>
                                            <td>{{ $item->libelle }}</td>
                                            <td>{{ $item->montant }} F CFA</td>
                                            <td>
                                                <span class="label label-success">Effectue</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane" id="kyc">
                            <form class="form-horizontal" action="/complete/kyc/admin/{{ $userClient->kycClient->id }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="content">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Entrez nom *</label>
                                                <input type="text" id="identite" value="{{ $userClient->kycClient->name }}" name="name" required class="form-control" placeholder="Nom ">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Entrez prenom *</label>
                                                <input type="text" id="identite" value="{{ $userClient->kycClient->lastname }}" name="lastname" required class="form-control" placeholder="Prénoms">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Entrez adresse email *</label>
                                                <input type="email" id="email" value="{{ $userClient->kycClient->email }}" name="email" required class="form-control" placeholder="Email">
                                            </div>
                                        </div>
                                        <div class="col-md-4">                                            
                                            <label for="recipient-name" class="control-label">Telephone</label>
                                            <div class="form-group">
                                                <input id="phone" value="{{$userClient->kycClient->telephone}}" required name="telephone" type="tel" value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Date de naissance *</label>
                                                <input required type="date" value="{{ $birthday }}" id="birthday" name="birthday" placeholder="Cliquer pour choisir" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Pays *</label>
                                                <select id="country" required name="country" class="form-control select2bs4" style="width:100%">
                                                    <option selected="selected" value="">Sélectionnez...</option>
                                                    @foreach ($countries as $key => $value)
                                                        <option @if(strtoupper($userClient->kycClient->country) == $key) selected @endif value="{{ $key }}">{{ $value['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Choisissez département *</label>
                                                <select id="dep" required name="departement" class="form-control select2bs4" style="width:100%">
                                                    <option selected="selected" value="">Sélectionnez...</option>
                                                    <option @if($userClient->kycClient->departement == 'AL') selected @endif value="AL">Alibori</option>
                                                    <option @if($userClient->kycClient->departement == 'AK') selected @endif value="AK">Atacora</option>
                                                    <option @if($userClient->kycClient->departement == 'AQ') selected @endif value="AQ">Atlantique</option>
                                                    <option @if($userClient->kycClient->departement == 'BO') selected @endif value="BO">Borgou</option>
                                                    <option @if($userClient->kycClient->departement == 'CO') selected @endif value="CO">Collines</option>
                                                    <option @if($userClient->kycClient->departement == 'KO') selected @endif value="KO">Couffo</option>
                                                    <option @if($userClient->kycClient->departement == 'DO') selected @endif value="DO">Donga</option>
                                                    <option @if($userClient->kycClient->departement == 'LI') selected @endif value="LI">Littoral</option>
                                                    <option @if($userClient->kycClient->departement == 'MO') selected @endif value="MO">Mono</option>
                                                    <option @if($userClient->kycClient->departement == 'OU') selected @endif value="OU">Ouémé</option>
                                                    <option @if($userClient->kycClient->departement == 'PL') selected @endif value="PL">Plateau</option>
                                                    <option @if($userClient->kycClient->departement == 'ZO') selected @endif value="ZO">Zou</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Entrez ville *</label>
                                                <input required id="ville" value="{{ $userClient->kycClient->city }}" name="city" type="text" required class="form-control" placeholder="Ville">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Entrez adresse de résidence *</label>
                                                <input required  type="text" value="{{ $userClient->kycClient->address }}" name="address" required class="form-control" id="adresse" placeholder="Adresse complète">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Entrez profession *</label>
                                                <input required type="text" name="job" value="{{ $userClient->kycClient->job }}" class="form-control" id="prof" placeholder="Profession">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Revenu mensuel *</label>
                                                <input required type="text" name="salary" value="{{ $userClient->kycClient->salary }}" class="form-control" id="revenu" placeholder="Profession">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Choisissez un type de pièce *</label>
                                                <select required class="form-control select2bs4" name="piece_type" id="piece" style="width:100%">
                                                    <option selected="selected" value="">Sélectionnez...</option>
                                                    <option @if(strtoupper($userClient->kycClient->piece_type) == '1') selected @endif value="1">CNI</option>
                                                    <option @if(strtoupper($userClient->kycClient->piece_type) == '2') selected @endif value="2">Passeport</option>
                                                    <option @if(strtoupper($userClient->kycClient->piece_type) == '3') selected @endif value="3">CIP</option>
                                                    <option @if(strtoupper($userClient->kycClient->piece_type) == '4') selected @endif value="4">Autres</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Numéro de la pièce *</label>
                                                <input required type="text" value="{{ $userClient->kycClient->piece_id }}" class="form-control" id="numpiece" name="piece_id" placeholder="ID de la pièce">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Fichier de la pièce *</label>
                                                <div class="input-group mb-3">
                                                    <div class="custom-file">
                                                        <input type="file" accept=".pdf,image/png, image/jpeg" name="piece_file" class="form-control">
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Fichier du client avec la pièce *</label>
                                                <div class="input-group mb-3">
                                                    <div class="custom-file">
                                                        <input type="file" accept=".pdf,image/png, image/jpeg" name="user_with_piece" class="form-control">
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <img src="{{ asset($userClient->kycClient->signature) }}" width="50px" alt="piece_file" srcset="">
                                        </div>
                                        <div class="col-md-4">
                                            <img src="{{ asset($userClient->kycClient->piece_file) }}" width="50px" alt="piece_file" srcset="">
                                        </div>
                                        <div class="col-md-4">
                                            <img src="{{ $userClient->kycClient->user_with_piece }}" width="50px" alt="user_with_piece" srcset="">
                                        </div>
                                    </div>
                                    
                                    @if (hasPermission('admin.kyc.edit'))
                                        <br>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-4">
                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" style="width: 100%" class="btn btn-primary">Modifier</button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="del-client-{{ $userClient->id }}" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1">Suppression de
                            {{ $userClient->lastname . ' ' . $userClient->name }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="/client/delete/{{ $userClient->id }}" method="POST">
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

        <div class="modal fade" id="reset-password-client-{{ $userClient->id }}" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1">Reinitialisation du mot de passe de
                            {{ $userClient->lastname . ' ' . $userClient->name }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="/client/reset/password/{{ $userClient->id }}" method="POST">
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

        <div class="modal fade" id="activation-client-{{ $userClient->id }}" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1">Activation de
                            {{ $userClient->lastname . ' ' . $userClient->name }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="/client/activation/{{ $userClient->id }}" method="POST">
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

        <div class="modal fade" id="desactivation-client-{{ $userClient->id }}" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1">Desactivation du mot de passe de
                            {{ $userClient->lastname . ' ' . $userClient->name }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="/client/desactivation/{{ $userClient->id }}" method="POST">
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
                                        
        <div class="modal fade" id="print-releve-client" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1">Impression du releve</h4>
                    </div>
                    <form action="/download/client/releve/{{$userClient->id}}" method="POST" id="form-print-releve">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Debut periode *</label>
                                        <input required type="date" id="debut-print-releve" name="debut" placeholder="Cliquer pour choisir" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Fin periode*</label>
                                        <input required type="date" id="fin-print-releve" name="fin" placeholder="Cliquer pour choisir" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="print-view" type="button" class="btn btn-info"><i class="fa fa-eye"></i> Voir le relevé</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>     

        <div class="modal fade" id="view-releve-client" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1" id="releve-title">Visualisation du releve du client</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row" id="releve-content">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="print-pdf" type="button" class="btn btn-danger"> <i class="fa fa-file-pdf-o"></i> Exporter PDF</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
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

        function getKeyByValue(object, value) {
            return Object.keys(object).find(key => object[key] === value);
        }

        var dateLibelle = {
            '01' : 'JAN',
            '02' : 'FEB',
            '03' : 'MAR',
            '04' : 'APR',
            '05' : 'MAY',
            '06' : 'JUN',
            '07' : 'JUL',
            '08' : 'AUG',
            '09' : 'SEP',
            '10' : 'OCT',
            '11' : 'NOV',
            '12' : 'DEC'
        }
        var birth = {{explode('-',$userClient->kycClient->birthday)[0]}}
        var birth = birth.length > 1 ? birth : '0'+birth
        var month = '{{explode('-',$userClient->kycClient->birthday)[1]}}'
        
        var birthMonth = getKeyByValue(dateLibelle,month);
        var birthYear = {{explode('-',$userClient->kycClient->birthday)[2]}}
        console.log(birthYear+'-'+birthMonth+'-'+birth)
        $('#birthday').val(birthYear+'-'+birthMonth+'-'+birth)
    </script>
    <script src="/plugins/intl-tel-input-master/build/js/intlTelInput.js"></script>
    <script>
        var input = document.querySelector("#phone");
        window.intlTelInput(input, {
            // allowDropdown: false,
            // autoInsertDialCode: true,
            // autoPlaceholder: "off",
            //containerClass: "tel-input",
            // countrySearch: false,
            // customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
            //   return "e.g. " + selectedCountryPlaceholder;
            // },
            // defaultToFirstCountry: false,
            // dropdownContainer: document.querySelector('#custom-container'),
            // excludeCountries: ["us"],
            // fixDropdownWidth: false,
            // formatAsYouType: false,
            // formatOnDisplay: false,
            // geoIpLookup: function(callback) {
            //   fetch("https://ipapi.co/json")
            //     .then(function(res) { return res.json(); })
            //     .then(function(data) { callback(data.country_code); })
            //     .catch(function() { callback(); });
            // },
            hiddenInput: () => "phone_full",
            // i18n: { 'de': 'Deutschland' },
            initialCountry: "bj",
            // nationalMode: false,
            // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
            // placeholderNumberType: "MOBILE",
            // preferredCountries: ['cn', 'jp'],
            // showFlags: false,
            // showSelectedDialCode: true,
            // useFullscreenPopup: true,
            utilsScript: "/plugins/intl-tel-input-master/build/js/utils.js"
        });
        
        $('#print-view').on('click',function (e) {
            e.preventDefault()
            if($('#debut-print-releve').val() == '' || $('#fin-print-releve').val() == ''){
                toastr.warning("Renseigner le debut et la fin de la periode")
            }else{
                console.log("/view/client/releve/"+'{{$userClient->id}}')
                $.ajax({
                    url: "/view/client/releve/"+'{{$userClient->id}}',
                    data: {
                        debut : $('#debut-print-releve').val(),
                        fin : $('#fin-print-releve').val()
                    },
                    method: "post",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data){
                        console.log(data)
                        $('#releve-content').html(data)
                        $('#view-releve-client').modal('show')
                        $("#example2").DataTable({
                            ordering: false
                        });
                    }
                }); 
            }
            
        })
        
        $('#print-pdf').on('click',function (e) {
            e.preventDefault()
            $('#form-print-releve').submit()
        })
    </script>
@endsection

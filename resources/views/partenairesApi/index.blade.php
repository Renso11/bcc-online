@extends('base')
@section('css')
    <link rel="stylesheet" href="/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endsection
@section('page')
    Liste des partenaires
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn waves-effect waves-light btn-primary" data-toggle="modal" data-target="#add-partenaire">Ajouter un partenaire</button>
                    <br>
                    <br>
                    <div class="card">
                        <div class="card-header border-0">
                        <h3 class="card-title">Liste des partenaires</h3>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Libelle</th>
                                        <th>Name</th>
                                        <th>Lastname</th>
                                        <th>Telephone</th>
                                        <th>Email</th>
                                        <th>Adresse</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($partenaires as $item)
                                        <tr>
                                            <td>{{ $item->libelle }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->lastname }}</td>
                                            <td>{{ $item->telephone }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->address }}</td>
                                            <td>{{ $item->balance }} F CFA</td>                                          
                                            <td>@if($item->status == 0) Inactif @else Actif @endif</td>                                            
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="/partenaire/details/{{ $item->id }}"><i class="fa fa-eye"></i> Détails sur le partenaire</a>
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#edit-partenaire-{{ $item->id }}"><i class="fa fa-edit"></i>&nbsp;&nbsp;Modifier les informations</a>
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#del-partenaire-{{ $item->id }}"><i class="fa fa-trash"></i>&nbsp;&nbsp;Supprimer le partenaire</a>
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#recharge-partenaire-{{ $item->id }}"><i class="fa fa-money-bill"></i>&nbsp;&nbsp;Initier un approvisionnement</a>
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
        </div>

        <div class="modal fade" id="add-partenaire" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1">Nouveau partenaire</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="/partenaires/api/add" method="POST" enctype="multipart/form-data"> 
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">  
                                <label for="recipient-name" class="control-label">Libelle du partenaire:</label>
                                <input type="text" autocomplete="off" required class="form-control" name="libelle">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="recipient-name" class="control-label">Email</label>
                                        <input type="text" autocomplete="off" required class="form-control" name="email">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="recipient-name" class="control-label">Téléphone (avec le code sans +)</label>
                                        <input type="text" autocomplete="off" required class="form-control" name="telephone">
                                    </div>
                                </div>
                            </div>    
                            <div class="form-group">  
                                <label for="recipient-name" class="control-label">Adresse:</label>
                                <input type="text" autocomplete="off" class="form-control" name="address">
                            </div> 
                            <div class="form-group">  
                                <label for="recipient-name" class="control-label">Nom du representant:</label>
                                <input type="text" autocomplete="off" class="form-control" name="name">
                            </div>
                            <div class="form-group">  
                                <label for="recipient-name" class="control-label">Prenom du representant:</label>
                                <input type="text" autocomplete="off" class="form-control" name="lastname">
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

        @foreach ($partenaires as $item)
            <div class="modal fade" id="vente-partenaire-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel1">Initiation de vente de carte virtuelle à {{ $item->libelle }}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <form action="/partenaire/vente/{{ $item->id }}" method="POST"> 
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">  
                                    <label for="vente-montant" class="control-label">Montant reçu:</label>
                                    <input type="number" value="" required class="form-control" name="montant">
                                </div>
                                <div class="form-group">  
                                    <label for="vente-number" class="control-label">Nombre de carte:</label>
                                    <input type="number" value="" required class="form-control" name="nombre">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Inititier</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="recharge-partenaire-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel1">Initiation d'approvisionnement pour {{ $item->libelle }}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <form action="/partenaires/api/recharge/{{ $item->id }}" method="POST"> 
                            @csrf   
                            <div class="modal-body">
                                <div class="form-group">  
                                    <label for="recipient-name" class="control-label">Montant:</label>
                                    <input type="number" value="" tocomplete="off" class="form-control" name="montant">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Initier</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="edit-partenaire-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel1">Modification de {{ $item->libelle }}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <form action="/partenaire/edit/{{ $item->id }}" method="POST"> 
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">  
                                    <label for="recipient-name" class="control-label">Libelle du partenaire:</label>
                                    <input type="text" value="{{ $item->libelle }}" autocomplete="off" class="form-control" name="libelle">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="recipient-name" class="control-label">CustomerID</label>
                                            <input type="text" value="{{ $item->code }}" autocomplete="off" required class="form-control" name="code">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="recipient-name" class="control-label">4 derniers chiffres</label>
                                            <input type="text" value="{{ $item->last }}" autocomplete="off" required class="form-control" name="last">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="recipient-name" class="control-label">Email</label>
                                            <input type="text" value="{{ $item->email }}" autocomplete="off" required class="form-control" name="email">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="recipient-name" class="control-label">Téléphone</label>
                                            <input type="text" value="{{ $item->telephone }}" autocomplete="off" required class="form-control" name="telephone">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">  
                                    <label for="recipient-name" class="control-label"> Fichier du RCCM <small>(uniquement pour changer)</small></label>
                                    <input type="file" autocomplete="off" class="form-control" name="rccm">
                                </div>
                                <div class="form-group">  
                                    <label for="recipient-name" class="control-label">Fichier du IFU<small>(uniquement pour changer)</small></label>
                                    <input type="file" autocomplete="off" class="form-control" name="ifu">
                                </div>
                                <div class="row">
                                    <table class="table table-bordered table-striped example1">
                                        <thead>
                                            <tr>
                                                <th>Type fichier</th>
                                                <th>Fichier</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>RCCM</td>
                                                <td>
                                                    <a target="_blank" href="{{ asset($item->rccm) }}" class="btn btn-primary">
                                                        <i class="fa fa-eye"></i> Voir le fichier
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>IFU</td>
                                                <td>
                                                    <a target="_blank" href="{{ asset($item->ifu) }}" class="btn btn-primary">
                                                        <i class="fa fa-eye"></i> Voir le fichier
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Modifier</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="del-partenaire-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel1">Suppression de {{ $item->libelle }}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <form action="/partenaire/delete/{{ $item->id }}" method="POST"> 
                            @csrf
                            <div class="modal-body">
                                <p>Etes vous sur de supprimer ce partenaire?</p>
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
    </section>
@endsection

@section('js')
    <!--This page plugins -->
    <script src="/plugins/select2/js/select2.full.min.js"></script>

    <!-- DataTables  & Plugins -->
    <script src="/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="/plugins/jszip/jszip.min.js"></script>
    <script src="/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script>
        $(".example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

    </script>
@endsection
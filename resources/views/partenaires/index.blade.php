@extends('base')
@section('css')
<link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="/plugins/bs-stepper/css/bs-stepper.min.css">
@endsection
@section('title')
    Liste des partenaires
@endsection
@section('page')
    Liste des partenaires
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @if (hasPermission('admin.partenaire.add'))
                        <a href="/partenaire/new" class="btn waves-effect waves-light btn-primary">Ajouter un partenaire</a>
                        <br>
                        <br>
                    @endif
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Liste des partenaires </h3>
                        </div>

                        <div class="box-body">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Libelle</th>
                                        <th>RCCM</th>
                                        <th>IFU</th>
                                        <th>Solde dist.</th>
                                        <th>Solde com.</th>
                                        <th>Email</th>
                                        <th>Telephone</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($partenaires as $item)
                                        <tr>
                                            <td>{{ $item->libelle }}</td>
                                            <td>{{ $item->num_rccm }}</td>
                                            <td>{{ $item->num_ifu }}</td>   
                                            <td>{{ $item->accountDistribution->solde }} F CFA</td>
                                            <td>{{ $item->accountCommission->solde }} F CFA</td>                                            
                                            <td>{{ $item->email }}</td>                                            
                                            <td>{{ $item->telephone }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default btn-flat">Actions</button>
                                                    <button type="button" class="btn btn-default btn-flat dropdown-toggle"
                                                        data-toggle="dropdown">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        @if (hasPermission('admin.partenaire.details'))
                                                            <li>
                                                                <a class="dropdown-item" href="/partenaire/details/{{ $item->id }}"><i class="fa fa-eye"></i> Details partenaire</a>
                                                            </li>
                                                        @endif
                                                        @if (hasPermission('admin.partenaire.users'))
                                                            <li>
                                                                <a class="dropdown-item" href="/partenaire/users/{{ $item->id }}"><i class="fa fa-users"></i> Utilisateurs partenaire</a>
                                                            </li>
                                                        @endif
                                                        @if (hasPermission('admin.partenaire.recharge.init'))
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#recharge-partenaire-{{ $item->id }}"><i class="fa fa-money"></i> Initier un rechargement</a>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <a class="dropdown-item" href="/partenaire/edit/{{ $item->id }}"><i class="fa fa-edit"></i> Modifier partenaire</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#del-partenaire-{{ $item->id }}"><i class="fa fa-trash"></i> Supprimer partenaire</a>
                                                        </li>
                                                    </ul>
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
        
        @foreach ($partenaires as $item)
            <div class="modal fade" id="recharge-partenaire-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel1">Rechargement de {{ $item->libelle }}</h4>
                        </div>
                        <form action="/partenaire/recharge/init/{{ $item->id }}" method="POST"> 
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

            <div class="modal fade" id="del-partenaire-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel1">Suppression de {{ $item->libelle }}</h4>
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
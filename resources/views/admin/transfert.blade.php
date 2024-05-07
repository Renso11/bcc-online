@extends('base')
@section('css')
<link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endsection
@section('title')
    Liste des transferts admin
@endsection
@section('page')
    Liste des transferts admin
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn waves-effect waves-light btn-primary" data-toggle="modal" data-target="#add-transfert">Nouveau transfert</button>
                    <br>
                    <br>
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Liste des transferts admin </h3>
                        </div>

                        <div class="box-body">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Compte GTP</th>
                                        <th>Program ID</th>
                                        <th>Sens</th>
                                        <th>Customer ID</th>
                                        <th>Last digits</th>
                                        <th>Montant</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transferts as $item)
                                        <tr>
                                            <td>{{ $item->compte }}</td>
                                            <td>{{ $item->program }}</td>
                                            <td>{{ $item->sens }}</td>
                                            <td>{{ $item->customer_id }}</td>
                                            <td>{{ $item->last_digits }}</td>
                                            <td>{{ $item->montant }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="add-transfert" role="dialog" aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog" role="document">
                <form action="/transfert/admin/add" method="POST" enctype="multipart/form-data"> 
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel1">Nouveau transfert</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Que voulez vous faire ?</label>
                                        <select class="form-control select2bs4" name="sens">
                                            <option value="debit">Envoyer vers la carte</option>
                                            <option value="credit">Retirer de la carte</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Compte GTP ID</label>
                                        <input type="text" required class="form-control" name="compte" placeholder="Compte GTP ID">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">4 dernier compte GTP</label>
                                        <input type="text" required class="form-control" name="compte_last" placeholder="4 dernier compte GTP">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Montant de l'operation</label>
                                        <input type="text" required class="form-control" name="montant" placeholder="Montant debut">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Customer ID  (Carte ou compte)</label>
                                        <input type="text" required class="form-control" name="customer_id" placeholder="Montant debut">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">4 dernier chiffre (Carte ou compte)</label>
                                        <input type="text" required class="form-control" name="last_digits" placeholder="Montant fin">
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                            <button type="submit" class="btn btn-primary">Commencer</button>
                        </div>
                    </div>
                </form>  
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
@extends('base')
@section('css')
    <link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endsection
@section('page')
    Détails du partenaire
@endsection
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <h3 class="profile-username text-center">{{ $partenaire->libelle }}</h3>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>Téléphone</b> <a class="pull-right">{{ $partenaire->telephone }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>RCCM</b> <a class="pull-right">{{ $partenaire->num_rccm }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>IFU</b> <a class="pull-right">{{ $partenaire->num_ifu }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Solde distribution</b> <a class="pull-right">{{ $partenaire->accountDistribution->solde }} <small>XOF</small></a>
                            </li>
                            <li class="list-group-item">
                                <b>Solde commission</b> <a class="pull-right">{{ $partenaire->accountCommission->solde }} <small>XOF</small></a>
                            </li>
                        </ul>                         
                        <button type="button" data-toggle="modal" data-target="#print-releve-partenaire" class="btn btn-primary">
                            <i class="fa fa-file-pdf-o"></i> Relevé
                        </button>                       
                        <a href="/partenaire/compte/distribution/{{ $partenaire->id }}" class="btn btn-success">
                            <i class="fa fa-money"></i> Cpt. Dist.
                        </a>               
                        <a href="/partenaire/compte/commission/{{ $partenaire->id }}" class="btn btn-danger">
                            <i class="fa fa-money"></i> Cpt. Comm.
                        </a>      
                    </div>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header p-2">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#depot" data-toggle="tab">Depots</a></li>
                                <li><a href="#retrait" data-toggle="tab">Retraits</a></li>
                                <li><a href="#appro" data-toggle="tab">Approvisionnements</a></li>
                                <li><a href="#cashout" data-toggle="tab">Cashouts</a></li>
                                <li><a href="#cession" data-toggle="tab">Cessions</a></li>
                                <li><a href="#card" data-toggle="tab">Vente de carte</a></venduli>
                            </ul>

                            <div class="tab-content">
                                <div class="active tab-pane" id="depot">
                                    <div class="card">
                                        <div class="card-body ">
                                            <table class="table table-bordered table-striped example1">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Client</th>
                                                        <th>Telephone</th>
                                                        <th>Montant</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($depots as $item)     
                                                        <tr>
                                                            <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                                            <td>{{ $item->userClient->name.' '.$item->userClient->lastname }}</td>
                                                            <td>{{ $item->userClient->username }}</td>
                                                            <td>{{ $item->montant }} F CFA</td>
                                                            <td>
                                                                @if($item->status == 'pending') 
                                                                    <span class="label label-warning">En attente</span> 
                                                                @elseif($item->status == 'completed') 
                                                                    <span class="label label-success">Finalisé</span> 
                                                                @elseif($item->status == 'refunded')
                                                                    <span class="label label-default">Remboursé</span> 
                                                                @elseif($item->status == 'cancelled')
                                                                    <span class="label label-danger">Annulé</span> 
                                                                @else
                                                                    <span class="label label-danger">Echec</span> 
                                                                @endif
                                                            </td>
                                                        </tr>  
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="tab-pane" id="retrait">
                                    <div class="card">
                                        <div class="card-body ">
                                            <table class="table table-bordered table-striped example1">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Client</th>
                                                        <th>Telephone</th>
                                                        <th>Montant</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($retraits as $item)     
                                                        <tr>
                                                            <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                                            <td>{{ $item->userClient->name.' '.$item->userClient->lastname }}</td>
                                                            <td>{{ $item->userClient->username }}</td>
                                                            <td>{{ $item->montant }} F CFA</td>
                                                            <td>
                                                                @if($item->status == 'pending') 
                                                                    <span class="label label-warning">En attente</span> 
                                                                @elseif($item->status == 'completed') 
                                                                    <span class="label label-success">Finalisé</span> 
                                                                @elseif($item->status == 'refunded')
                                                                    <span class="label label-default">Remboursé</span> 
                                                                @elseif($item->status == 'cancelled')
                                                                    <span class="label label-danger">Annulé</span> 
                                                                @else
                                                                    <span class="label label-danger">Echec</span> 
                                                                @endif
                                                            </td>
                                                        </tr>  
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="tab-pane" id="appro">
                                    <div class="card">
                                        <div class="card-body ">
                                            <table class="table table-bordered table-striped example1">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Methode paiement</th>
                                                        <th>Solde avant</th>
                                                        <th>Montant</th>
                                                        <th>Solde apres</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($appros as $item)
                                                        <tr>
                                                            <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                                                            <td>{{ $item->wallet->type }}</td>
                                                            <td>{{ $item->solde_avant }} F CFA</td>
                                                            <td>{{ $item->montant }} F CFA</td>
                                                            <td>{{ $item->solde_apres }} F CFA</td>                                                            
                                                            <td>
                                                                @if($item->status == 'pending') 
                                                                    <span class="label label-warning">En attente</span> 
                                                                @elseif($item->status == 'completed') 
                                                                    <span class="label label-success">Finalisé</span> 
                                                                @elseif($item->status == 'refunded')
                                                                    <span class="label label-default">Remboursé</span> 
                                                                @elseif($item->status == 'cancelled')
                                                                    <span class="label label-danger">Annulé</span> 
                                                                @else
                                                                    <span class="label label-danger">Echec</span> 
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="tab-pane" id="cashout">
                                    <div class="card">
                                        <div class="card-body ">
                                            <table class="table table-bordered table-striped example1">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Methode paiement</th>
                                                        <th>Solde avant</th>
                                                        <th>Montant</th>
                                                        <th>Solde apres</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($cashouts as $item)
                                                        <tr>
                                                            <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                                                            <td>{{ $item->wallet->type }}</td>
                                                            <td>{{ $item->solde_avant }} F CFA</td>
                                                            <td>{{ $item->montant }} F CFA</td>
                                                            <td>{{ $item->solde_apres }} F CFA</td>                                                            
                                                            <td>
                                                                @if($item->status == 'pending') 
                                                                    <span class="label label-warning">En attente</span> 
                                                                @elseif($item->status == 'completed') 
                                                                    <span class="label label-success">Finalisé</span> 
                                                                @elseif($item->status == 'refunded')
                                                                    <span class="label label-default">Remboursé</span> 
                                                                @elseif($item->status == 'cancelled')
                                                                    <span class="label label-danger">Annulé</span> 
                                                                @else
                                                                    <span class="label label-danger">Echec</span> 
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="tab-pane" id="cession">
                                    <div class="card">
                                        <div class="card-body ">
                                            <table class="table table-bordered table-striped example1">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Partenaire receveur</th>
                                                        <th>Montant</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($cessions as $item)
                                                        <tr>
                                                            <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                                                            <td>{{ $item->receiver->libelle }}</td>   
                                                            <td>{{ $item->montant }} F CFA</td>                                    
                                                            <td>
                                                                @if($item->status == 'pending') 
                                                                    <span class="label label-warning">En attente</span> 
                                                                @elseif($item->status == 'completed') 
                                                                    <span class="label label-success">Finalisé</span> 
                                                                @elseif($item->status == 'refunded')
                                                                    <span class="label label-default">Remboursé</span> 
                                                                @elseif($item->status == 'cancelled')
                                                                    <span class="label label-danger">Annulé</span> 
                                                                @else
                                                                    <span class="label label-danger">Echec</span> 
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="tab-pane" id="card">
                                    <div class="card">
                                        <div class="card-body ">
                                            <table class="table table-bordered table-striped example1">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Client</th>
                                                        <th>Customer ID</th>
                                                        <th>4 derniers chiffres</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($cards as $item)
                                                        <tr>
                                                            <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                                                            <td>{{ $item->userCard->userClient->name.' '.$item->userCard->userClient->lastname }}</td>
                                                            <td>{{ $item->userCard->customer_id }}</td>
                                                            <td>{{ $item->userCard->last_digits }}</td>                                    
                                                            <td>
                                                                @if($item->status == 'pending') 
                                                                    <span class="label label-warning">En attente</span> 
                                                                @elseif($item->status == 'completed') 
                                                                    <span class="label label-success">Finalisé</span> 
                                                                @elseif($item->status == 'refunded')
                                                                    <span class="label label-default">Remboursé</span> 
                                                                @elseif($item->status == 'cancelled')
                                                                    <span class="label label-danger">Annulé</span> 
                                                                @else
                                                                    <span class="label label-danger">Echec</span> 
                                                                @endif
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
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        
                                        
        <div class="modal fade" id="print-releve-partenaire" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1">Impression du releve</h4>
                    </div>
                    <form action="/download/partenaire/revele/{{$partenaire->id}}" method="POST" id="form-print-releve">
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
                                        <label for="exampleInputPassword1">Fin  periode*</label>
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

        <div class="modal fade" id="view-releve-partenaire" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1" id="releve-title">Visualisation du releve du partenaire</h4>
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

    <script src="/plugins/select2/js/select2.full.min.js"></script>

    <script src="/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="/excel/src/jquery.table2excel.js"></script> 
    <script>
        $(".example1").DataTable({
            ordering: false
        });
        
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })
        
        $('#print-view').on('click',function (e) {
            e.preventDefault()
            if($('#debut-print-releve').val() == '' || $('#fin-print-releve').val() == ''){
                toastr.warning("Renseigner le debut et la fin de la periode")
            }else{
                $.ajax({
                    url: "/view/partenaire/revele/"+'{{$partenaire->id}}',
                    data: {
                        debut : $('#debut-print-releve').val(),
                        fin : $('#fin-print-releve').val()
                    },
                    method: "post",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data){
                        $('#releve-content').html(data)
                        $('#view-releve-partenaire').modal('show')
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
@extends('base')
@section('css')
<link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
@endsection
@section('page')
    Liste des operations clients finalises
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <br>
                    <br>
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Liste des operations clients finalisées </h3>
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
                                @foreach($transactions as $item)
                                    <tr>
                                        <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                        <td>{{ $item->userClient ? $item->userClient->name.' '.$item->userClient->lastname : '-' }}</td>
                                        <td>{{ $item->type }}</td>
                                        <td>{{ $item->moyen_paiement }}</td>
                                        <td>{{ $item->montant }}</td>
                                        <td>{{ $item->frais}}</td>
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
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#show-transaction-{{ $item->id }}"><i class="fa fa-eye"></i>&nbsp;&nbsp;Voir plus</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="show-transaction-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Détails de la transaction</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label for="">Date d'operation</label>
                                                            <p>{{ $item->created_at->format('d-m-Y H:i:s') }}</p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="">Nom et prénoms</label>
                                                            <p>{{ $item->userClient ? $item->userClient->name.' '.$item->userClient->lastname : '-' }}</p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="">Telephone</label>
                                                            <p>{{ $item->userClient ? '+'.$item->userClient->username : '-' }}</p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="">Type d'opération</label>
                                                            <p>{{ $item->type }}</p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="">Methode de paiement</label>
                                                            <p>{{ $item->moyen_paiement }}</p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="">Reference du paiement</label>
                                                            <p>{{ $item->reference }}</p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="">Montant</label>
                                                            <p>{{ $item->montant }}</p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="">Frais</label>
                                                            <p>{{ $item->frais }}</p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="">Status du paiement</label>
                                                            <p>{{ $item->is_debited == 1 ? 'Payée' : 'Non payée' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
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
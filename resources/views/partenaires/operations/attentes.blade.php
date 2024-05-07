@extends('base')
@section('css')
<link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
@endsection
@section('page')
    Liste des operations partenaires en attentes
@endsection
@section('title')
    Operations partenaires en attentes
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
                            <h3 class="box-title">Liste des operations partenaires en attentes </h3>
                        </div>
    
                        <div class="box-body">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th style="width:15%">Date</th>
                                        <th style="width:15%">Partenaire</th>
                                        <th style="width:15%">Client</th>
                                        <th style="width:10%">Type</th>
                                        <th style="width:10%">Montant</th>
                                        <th style="width:10%">Frais</th>
                                        <th style="width:10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>        
                                    @foreach($transactions as $item)
                                        <tr>
                                            <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                            <td>{{ $item->partenaire ? $item->partenaire->libelle : '-'  }}</td>
                                            <td>{{ $item->userClient ? $item->userClient->lastname.' '.$item->userClient->name : '-'  }}</td>
                                            <td>{{ $item->type }}</td>
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
                                                        @if (hasPermission('admin.partenaire.valide.recharge'))    
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#show-transaction-{{ $item->id }}"><i class="fa fa-eye"></i>&nbsp;&nbsp;Voir plus</a>
                                                            </li>
                                                        @endif  
                                                        @if (hasPermission('admin.partenaire.operations.attentes.refund'))    
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#refund-transaction-{{ $item->id }}"><i class="fa fa-spinner"></i>&nbsp;&nbsp;Rembourser la transaction</a>
                                                            </li>
                                                        @endif
                                                        @if (hasPermission('admin.partenaire.operations.attentes.cancel'))    
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#cancel-transaction-{{ $item->id }}"><i class="fa fa-times"></i>&nbsp;&nbsp;Annuler la transaction</a>
                                                            </li>
                                                        @endif
                                                        @if (hasPermission('admin.partenaire.operations.attentes.cancel'))    
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#complete-transaction-{{ $item->id }}"><i class="fa fa-check"></i>&nbsp;&nbsp;Finaliser la transaction</a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>

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
                                                                    <label for="">Partenaire</label>
                                                                    <p>{{ $item->partenaire ? $item->partenaire->libelle : '-' }}</p>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="">Nom et prénoms du client</label>
                                                                    <p>{{ $item->userClient ? $item->userClient->lastname.' '.$item->userClient->name : '-' }}</p>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="">Telephone</label>
                                                                    <p>{{ $item->userClient ? '+'.$item->userClient->username : '-'}}</p>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="">Type d'opération</label>
                                                                    <p>{{ $item->type }}</p>
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
                                            
                                            <div class="modal fade" id="complete-transaction-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="exampleModalLabel1">Finalisation de la transaction</h4>
                                                        </div>
                                                        <form action="/partenaire/operations/attentes/complete" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <input type="hidden" name="id" value="{{ $item->id}}">
                                                                <input type="hidden" name="type_operation" value="{{ $item->type}}">
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
                                            
                                            <div class="modal fade" id="refund-transaction-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                                <div class="modal-dialog modal-sm" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="exampleModalLabel1">Remboursement de la transaction</h4>
                                                        </div>
                                                        <form action="/partenaire/operations/attentes/refund" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <p>Etes vous sur de rembourser cette transaction?</p>
                                                                <input type="hidden" name="id" value="{{ $item->id}}">
                                                                <input type="hidden" name="type_operation" value="{{ $item->type}}">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                                <button type="submit" class="btn btn-primary">Oui</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="modal fade" id="cancel-transaction-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                                <div class="modal-dialog modal-sm" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="exampleModalLabel1">Annulation de la transaction</h4>
                                                        </div>
                                                        <form action="/partenaire/operations/attentes/cancel" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <p>Etes vous sur d'annuler cette transaction?</p>
                                                                <input type="hidden" name="id" value="{{ $item->id}}">
                                                                <input type="hidden" name="type_operation" value="{{ $item->type}}">
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
                                            
                                        </tr>
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

        $('#code').on('keyup',function (e) {
            var code = $(this).val();
            $('#name').val("")
            $('#lastname').val("")
            $('#last').val("")
            if(code.length >= 8){
                $('#loader').show()
                $.ajax({
                    url: "/search/client",
                    data: {
                        code : code
                    },
                    method: "post",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data){
                        let res = JSON.parse(data)
                        $('#loader').hide()
                        $('#name').val(res.firstName)
                        $('#lastname').val(res.lastname)
                        $('#last').val(res.lastFourDigits)
                    }
                }); 
            }
        })
        
    </script>
@endsection
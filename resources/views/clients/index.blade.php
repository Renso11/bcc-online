@extends('base')
@section('css')
<link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
@endsection
@section('page')
    Liste des clients
@endsection
@section('title')
    Liste des clients
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Liste des comptes clients validées </h3>
                        </div>

                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">Debut</label>
                                    <input type="datetime-local" class="form-control" id="debut" name="debut">
                                </div>
                                <div class="col-md-6">
                                    <label for="">Fin</label>
                                    <input type="datetime-local" class="form-control" id="fin" name="fin">
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                </div>
                                <div class="col-md-4">
                                    <button id="btn-search" class="btn btn-info" style="width: 100%">
                                        <i class="fa fa-search"></i> Rechercher
                                    </button>
                                </div>
                            </div>
                            <br>
                            <div id="resultat">
                                <table class="table table-bordered table-striped example1">
                                    <thead>
                                        <tr>
                                            <th>Date de creation</th>
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
                                                            <li>
                                                                <a class="dropdown-item" href="/client/details/{{ $item->id }}"><i class="fa fa-eye"></i> Détails sur le compte</a>
                                                            </li>
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
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="exampleModalLabel1">Suppression de {{ $item->lastname.' '.$item->name }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="exampleModalLabel1">Reinitialisation du mot de passe de {{ $item->lastname.' '.$item->name }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="exampleModalLabel1">Activation de {{ $item->lastname.' '.$item->name }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="exampleModalLabel1">Désactivation de {{ $item->lastname.' '.$item->name }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel1">Validation du dossier de {{ $item->lastname.' '.$item->name }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel1">Rejet du dossier de {{ $item->lastname.' '.$item->name }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                            

                            <div class="mt-3"><nav aria-label="Page navigation example">
                                {!! $userClients->links('pagination.custom') !!} 
                            </div>
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

        $('#code').on('keyup',function (e) {
            var code = $(this).val();
            $('#name').val("")complete/kyc/admin
            $('#lastname').val("")
            $('#last').val("")
            if(code.length >= 8){
                $('#loader').show()
                $.ajax({
                    url: "/search/client",
                    data: {
                        debut : $('#debut').val(),
                        fin : $('#fin').val()
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
        
        $('#btn-search').on('click',function(e) {
            e.preventDefault()
            var $this = $(this)
            $this.prop('disabled',true)
            $('#resultat').hide()
            $this.html('<i class="fa fa-spinner fa-spin"></i> Recherche...')
            var formData = new FormData($('#form-search')[0]);
            var type_operation  = $('#type_operation').val();
            var status = $('#status').val();
            
            /*if(type_operation == "" || status == ""){
                toastr.warning("Veuillez renseigner le type d'operation et/ou le status")
                $this.html(`<i class="fa fa-search"></i> Rechercher</button>`)
                $this.prop('disabled',false)
            }else{*/
            $.ajax({
                url: "/search/compte/client",
                data: {
                    debut : $('#debut').val(),
                    fin : $('#fin').val()
                },
                method: "post",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(data){
                    $('#resultat').html(data)
                    $('#resultat').show()
                    $this.html(`<i class="fa fa-search"></i> Rechercher</button>`)
                    $this.prop('disabled',false)
                    
                    $(".example1").DataTable({
                        ordering: false
                    });
                    
                    $(".exportToExcel").click(function (e) {
                        $(".example1").DataTable().destroy() 
                        var $tableClone = $("#all-table").clone();
                        $('#noExel').show();
                        $('.bold').css("font-weight", "bold");

                        $($tableClone).table2excel({
                            exclude: ".noExl",
                            name: "Rapport des transactions des clients",
                            filename: "Rapport des transactions des clients.xls",
                            fileext: ".xls",
                            exclude_img: true,
                            exclude_links: true,
                            exclude_inputs: true,
                            preserveColors: true
                        });
                        $('#noExel').hide();
                        $(".example1").DataTable({
                            ordering: false
                        });
                        $('.bold').removeAttr('style');
                    });
                }
            }); 
            //}
        })
        
    </script>
@endsection
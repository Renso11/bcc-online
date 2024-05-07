@extends('base')
@section('css')
    <link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endsection
@section('title')
    Liste des roles
@endsection
@section('page')
    Liste des roles
@endsection
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <button type="button" class="btn waves-effect waves-light btn-primary" data-toggle="modal" data-target="#add-role">Ajouter un role</button>
                <br>
                <br>
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Liste des roles </h3>
                    </div>

                    <div class="box-body">
                        <table class="table table-bordered table-striped example1">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Type</th>
                                    <th style="width: 65%">Permmisions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $item)
                                    <tr>
                                        <td>{{ $item->libelle }}</td>
                                        <td>{{ $item->type }}</td>
                                        <td>
                                            @foreach ($item->rolePermissions as $value)
                                                <span class="label" style="background: rgb(192, 188, 188)">{{ $value->permission ? $value->permission->libelle : 'oko' }}</span>
                                            @endforeach
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

                                    <div class="modal fade" id="edit-role-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Modification du role {{ $item->libelle }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/roles/edit/{{ $item->id }}" method="POST" id="form-edit-{{ $item->id }}"> 
                                                    @csrf
                                                    <input type="hidden" name="permissions" id="edit-liste-permissions-{{ $item->id }}">
                                                    <div class="modal-body">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="">Libellé</label>
                                                                        <input type="text" class="form-control" id="libelle-{{ $item->id }}" value="{{ $item->libelle }}" name="libelle" placeholder="Libellé du role">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="div-permissions">
                                                                <div class="form-group">
                                                                    <label for="">Permissions</label>
                                                                </div>
                                                                <div class="row">
                                                                    @foreach ($permissions as $value)
                                                                        <div class="col-md-6 div-check-permissions">
                                                                            <label for=""><input type="checkbox" @if(in_array($value->id,$item->rolePermissions->pluck('permission_id')->all())) checked @endif class="check-permissions-{{ $item->id }}" value="{{ $value->id }}"> {{ $value->libelle }} </label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                                                        <button type="button" class="btn btn-primary edit-role" data-id="{{ $item->id }}">Modifier</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="del-role-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Suppression du role {{ $item->libelle }} </h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <form action="/roles/delete/{{ $item->id }}" method="POST"> 
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Cela implique que tous les porteurs de ce role ne pourront plus utilisé l'application. <br> Etes vous sur de supprimer ce role ?</p>
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
        </div>
    </div>

    <div class="modal fade" id="add-role" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel1">Definition des roles</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="/roles/add" id="form-add" method="POST"> 
                    @csrf
                    <input type="hidden" name="permissions" id="add-liste-permissions">
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Libellé</label>
                                        <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Libellé du role">
                                        <input type="hidden" id="libelle" name="type" value="admin">
                                    </div>
                                </div>
                            </div>
                            <div class="div-permissions">
                                <div class="form-group">
                                    <label for="">Permissions</label>
                                    <br>
                                    <label for=""><input type="checkbox" id="check-all" value="all"> Tout selectionner </label>
                                    <br>
                                </div>
                                <div class="row">
                                    @foreach ($permissions as $value)
                                        <div class="col-md-6 div-check-permissions">
                                            <label for=""><input type="checkbox" class="check-permissions" value="{{ $value->id }}"> {{ $value->libelle }} </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-primary" id="add-btn-role">Enregistrer</button>
                    </div>
                </form>
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
    </script>
    <script>
        $('#add-btn-role').on('click',function(e){
            e.preventDefault()
            var search =  $('.check-permissions:checkbox:checked').map(function(){
                return $(this).val();
            }).get(); 
            if($('#libelle').val() == ''){
                toastr.warning("Renseigner le libelle")
            }else if(search.length == 0){
                toastr.warning("Choisisser au moins une permissions")
            }else{
                $('#add-liste-permissions').val(JSON.stringify(search))
                $('#form-add').submit()
            }
        })
            
        $('.edit-role').on('click',function(e){
            e.preventDefault()
            var id = $(this).attr('data-id')
            var search =  $('.check-permissions-'+id+':checkbox:checked').map(function(){
                return $(this).val();
            }).get();
            if($('#libelle-'+id).val() == ''){
                toastr.warning("Renseigner le libelle")
            }else if(search.length == 0){
                toastr.warning("Choisisser au moins une permissions")
            }else{
                $('#edit-liste-permissions-'+id).val(JSON.stringify(search))
                $('#form-edit-'+id).submit()
            }
        })
            
        $('#check-all').on('change',function(e){
            e.preventDefault()
            if($(this).is(":checked") == true){
                $(".check-permissions").prop("checked", true );
            }else{
                $(".check-permissions").prop("checked", false );
            }
        })

    </script>
@endsection
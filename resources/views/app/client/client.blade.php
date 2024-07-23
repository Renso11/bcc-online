@extends('base')
@section('title')
    Parametres application client
@endsection
@section('page')
    Parametres application client
@endsection
@section('css')

    <link rel="stylesheet" href="/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Infos cartes virtuelles </h3>
                        </div>

                        <div class="box-body">
                            <form action="/card/infos/update" id="form-add" method="POST">
                                @csrf 
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Nombre de cartes par personne</label>
                                            <input type="number" value="{{ $info_card ? $info_card->card_max : 0 }}" class="form-control" name="nb_card" placeholder="Nombre de cartes">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Prix unitaire de la carte virtuelle</label>
                                            <input type="number" value="{{ $info_card ? $info_card->card_price : 0}}" class="form-control" name="pu_card" placeholder="Prix unitaire">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary" style="width: 100%" id="add-role">Mettre à jour</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Infos cartes virtuelles </h3>
                        </div>

                        <div class="box-body">
                            <button type="button" class="btn waves-effect waves-light btn-primary" data-toggle="modal" data-target="#addService"> Nouveau module </button>
                            <br>
                            <br>
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Modules</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($services as $item)
                                        <tr id="row-{{ $item->id }}">
                                            <td>{{ $item->slug }}</td>
                                            <td class="status">@if($item->status == 1) <span class="label label-success">Actif</span> @else <span class="label label-danger">Inactif</span> @endif</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a class="btn btn-danger delete-module" href="javascript:void(0)" data-toggle="modal" data-target="#del-service-{{ $item->id }}"><i class="fa fa-trash"></i></a>
                                                    @if($item->status == 0) 
                                                        <a class="btn btn-success enable-module" href="javascript:void(0)" data-toggle="modal" data-target="#act-service-{{ $item->id }}"><i class="fa fa-check"></i></a>
                                                    @else
                                                        <a class="btn btn-warning disable-module" href="javascript:void(0)" data-toggle="modal" data-target="#des-service-{{ $item->id }}"><i class="fa fa-times"></i></a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="del-service-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="exampleModalLabel1">Suppression du module </h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <form action="#" id="form-delete-{{ $item->id }}" method="POST"> 
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Etes vous sur de supprimer ce module ?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                            <button type="button" class="btn btn-success btn-delete-module" data-id="{{ $item->id }}" class="btn btn-primary">Oui</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @if($item->status == 0) 
                                            <div class="modal fade" id="act-service-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="exampleModalLabel1">Activation du module </h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>
                                                        <form action="#" id="form-enable-{{ $item->id }}" method="POST"> 
                                                            @csrf
                                                            <div class="modal-body">
                                                                <p>Etes vous sur d'activer ce module ?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                                <button type="button" class="btn btn-success btn-enable-module" data-id="{{ $item->id }}" class="btn btn-primary">Oui</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="modal fade" id="des-service-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="exampleModalLabel1">Desactivation du module </h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>
                                                        <form action="#" id="form-disable-{{ $item->id }}" method="POST"> 
                                                            @csrf
                                                            <div class="modal-body">
                                                                <p>Etes vous sur de desactiver ce module ?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                                <button type="button" class="btn btn-success btn-disable-module" data-id="{{ $item->id }}" class="btn btn-primary">Oui</button>
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
                <div class="col-md-6">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Questions de reinitialisations de mot de passe </h3>
                        </div>

                        <div class="box-body">
                            <button type="button" class="btn waves-effect waves-light btn-primary" data-toggle="modal" data-target="#addQuestion"> Nouvelle question </button>
                            <br>
                            <br>
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Label</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($questions as $item)
                                        <tr id="row-question-{{ $item->id }}">
                                            <td>{{ $item->libelle }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a class="btn btn-danger delete-question" href="javascript:void(0)" data-toggle="modal" data-target="#del-question-{{ $item->id }}"><i class="fa fa-trash"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="del-question-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="exampleModalLabel1">Suppression de la question </h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <form action="#" id="form-delete-question-{{ $item->id }}" method="POST"> 
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Etes vous sur de supprimer cette question ?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Non</button>
                                                            <button type="button" class="btn btn-success btn-delete-question" data-id="{{ $item->id }}" class="btn btn-primary">Oui</button>
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
                            <h3 class="box-title">Conditions generales d'utilisation </h3>
                        </div>

                        <div class="box-body">
                            <form action="/save/param" method="POST" id="post-form">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" name="key_value" value="CGU" class="form-control" id="title" required>
                                    <label for="content">Content</label>
                                    <div id="editorjs"></div>
                                    <textarea name="content" class="cgu" style="display:none;"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Pricing </h3>
                        </div>

                        <div class="box-body">
                            <form action="/save/param" method="POST" id="post-form">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" name="key_value" value="pricing" class="form-control" id="title" required>
                                    <label for="content">Content</label>
                                    <div id="editorjs1"></div>
                                    <textarea name="content" class="pricing" style="display:none;"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Notifications </h3>
                        </div>

                        <div class="box-body">
                            <form action="/save/notification" method="POST" id="notification-form">
                                @csrf
                                <div class="form-group">
                                    <label for="title">Libelle</label>
                                    <input type="text" name="libelle" class="form-control" id="title" required>
                                </div>
                                <div class="form-group">
                                    <label for="title">Priorité</label>
                                    <select class="form-control select2bs4" required name="priorite" data-placeholder="Selectionner le niveau">
                                        <option value="">Selectionner le niveau </option>
                                        <option value="hot">Important</option>
                                        <option value="nothot">Moins important </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="content">Content</label>
                                    <div id="notificationEditorJs"></div>
                                    <textarea name="content" class="notification" style="display:none;"></textarea>
                                </div>
                                <button type="submit" id="notificationBtn" class="btn btn-primary">Enregistrer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        <div class="modal fade" id="addService" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog modal-xs" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1">Ajout de service </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="/service/client/add" id="form-add" method="POST"> 
                        @csrf
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Libelle</label>
                                            <input type="text" class="form-control" name="libelle" placeholder="Libelle du module">
                                        </div>
                                    </div>
                                </div>
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

        <div class="modal fade" id="addQuestion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog modal-xs" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1">Ajout de question </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="/question/add" id="form-add" method="POST"> 
                        @csrf
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Libelle</label>
                                            <input type="text" class="form-control" name="libelle" placeholder="Libelle de la question">
                                        </div>
                                    </div>
                                </div>
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

    </section>
@endsection

@section('js')
    <script src="/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <script src="/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>   

    <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
        .create(document.querySelector('.cgu'))
        .catch(error => {
            console.error(error);
        });
    </script>
    <script>
        ClassicEditor
        .create(document.querySelector('.pricing'))
        .catch(error => {
            console.error(error);
        });
    </script>
    <script>
        ClassicEditor
        .create(document.querySelector('.notification'))
        .catch(error => {
            console.error(error);
        });
    </script>

    <script>
        $(".example1").DataTable({
            ordering: false
        });

        $('.btn-delete-module').on('click',function(e){
            e.preventDefault()
            var $this = $(this);
            var id = $this.attr('data-id');
            var $btn = $("#row-"+id).find('.delete-module')
            var formData = new FormData($('#form-delete-'+id)[0]);
            $('#del-service-'+id).modal('hide')
            $btn.prop('disabled',true)
            $btn.html('<i class="fa fa-spinner fa-spin"></i>')
            $.ajax({
                url: "/service/delete/"+id,
                data: formData,
                processData: false,
                contentType: false,
                method: "post",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(data){ 
                    if(data == 'success'){ 
                        var row = $(".example1").DataTable().row($("#row-"+id)).remove().draw();
                        toastr.success("Module supprimé avec succes")
                    }else{
                        toastr.error("Echec de supression")
                    }
                }
            });
        })

        $('.btn-disable-module').on('click',function(e){
            e.preventDefault()
            var $this = $(this);
            var id = $this.attr('data-id');
            var $btn = $("#row-"+id).find('.disable-module')
            var $status = $("#row-"+id).find('.status')
            var formData = new FormData($('#form-disable-'+id)[0]);
            $('#des-service-'+id).modal('hide')
            $btn.prop('disabled',true)
            $btn.html('<i class="fa fa-spinner fa-spin"></i>')
            $.ajax({
                url: "/service/desactivate/"+id,
                data: formData,
                processData: false,
                contentType: false,
                method: "post",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(data){ 
                    if(data == 'success'){
                        $btn.prop('disabled',false)
                        $btn.html('<i class="fa fa-check"></i>')
                        $btn.removeClass('btn-warning')
                        $btn.addClass('btn-success')
                        $btn.removeClass('disable-module')
                        $btn.addClass('enable-module')
                        $status.html('<span class="label label-danger">Inactif</span>')
                        toastr.success("Module desactivé avec succes")
                    }else{
                        toastr.error("Echec de desactivation")
                    }
                }
            });
        })

        $('.btn-enable-module').on('click',function(e){
            e.preventDefault()
            var $this = $(this);
            var id = $this.attr('data-id');
            var $btn = $("#row-"+id).find('.enable-module')
            var $status = $("#row-"+id).find('.status')
            var formData = new FormData($('#form-enable-'+id)[0]);
            $('#act-service-'+id).modal('hide')
            $btn.prop('disabled',true)
            $btn.html('<i class="fa fa-spinner fa-spin"></i>')
            $.ajax({
                url: "/service/activate/"+id,
                data: formData,
                processData: false,
                contentType: false,
                method: "post",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(data){ 
                    if(data == 'success'){
                        $btn.prop('disabled',false)
                        $btn.html('<i class="fa fa-times"></i>')
                        $btn.addClass('btn-warning')
                        $btn.removeClass('btn-success')
                        $btn.addClass('disable-module')
                        $btn.removeClass('enable-module')
                        $status.html('<span class="label label-success">Actif</span>')
                        toastr.success("Module activé avec succes")
                    }else{
                        toastr.error("Echec d'activation")
                    }
                }
            });
        })

        $('.btn-delete-question').on('click',function(e){
            e.preventDefault()
            var $this = $(this);
            var id = $this.attr('data-id');
            var $btn = $("#row-question-"+id).find('.delete-question')
            var formData = new FormData($('#form-delete-'+id)[0]);
            $('#del-question-'+id).modal('hide')
            $btn.prop('disabled',true)
            $btn.html('<i class="fa fa-spinner fa-spin"></i>')
            $.ajax({
                url: "/question/delete/"+id,
                data: formData,
                processData: false,
                contentType: false,
                method: "post",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(data){ 
                    if(data == 'success'){ 
                        var row = $(".example1").DataTable().row($("#row-question-"+id)).remove().draw();
                        toastr.success("Question supprimé avec succes")
                    }else{
                        toastr.error("Echec de supression")
                    }
                }
            });
        })
    </script>
@endsection
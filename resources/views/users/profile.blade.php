@extends('base')
@section('css')
@endsection
@section('page')
    Mon profile
@endsection
@section('content')

<section class="content">
    <div class="row">
        <div class="col-md-3">

            <div class="box box-primary">
                <div class="box-body box-profile">
                    <h3 class="profile-username text-center">{{ $user->name . ' ' . $user->lastname }}</h3>
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>Username</b> <a class="pull-right">{{ '@'.$user->username }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Derniere connexion</b> <a class="pull-right">{{ $user->last_connexion }}</a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        <div class="col-md-9">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#info" data-toggle="tab">Informations</a></li>
                    <li><a href="#pass" data-toggle="tab">Mot de passe</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="info">
                        <form action="/profile/informations/edit" method="POST" class="form-horizontal form-material">
                            @csrf
                            <div class="form-group">
                                <label class="col-md-12">Nom</label>
                                <div class="col-md-12">
                                    <input type="text" placeholder="Nom" name="name" value="{{ $user->name }}" class="form-control form-control-line">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Prenom</label>
                                <div class="col-md-12">
                                    <input type="text" placeholder="Prenom" name="lastname" value="{{ $user->lastname }}" class="form-control form-control-line">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="example-email" class="col-md-12">Username</label>
                                <div class="col-md-12">
                                    <input type="text" placeholder="Username" name="username" value="{{ $user->username }}" class="form-control form-control-line"  id="example-email">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button class="btn btn-success">Modifier</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane" id="pass">
                        <form action="/profile/password/change" id="form-change-password" method="POST" class="form-horizontal form-material">
                            @csrf
                            <div class="form-group">
                                <label class="col-md-12">Nouveau mot de passe</label>
                                <div class="col-md-12">
                                    <input type="password" id="password" name="password" placeholder="Nouveau mot de passe" class="form-control form-control-line">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="example-email" class="col-md-12">Confirmer le mot de passe</label>
                                <div class="col-md-12">
                                    <input type="password" id="conf-password" placeholder="Confirmer le mot de passe" class="form-control form-control-line" name="example-email" id="example-email">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="button" class="btn btn-success" id="change-password">Modifier</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('js')
    <script>
        $('#change-password').on('click',function(e) {
            e.preventDefault()
            if($('#password').val() !== $('#conf-password').val()){
                toastr.warning("Les deux mots de passe ne correspondent pas")
            }else{
                $('#form-change-password').submit()
            }
        })
    </script>
@endsection

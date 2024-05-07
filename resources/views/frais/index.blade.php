@extends('base')
@section('css')
    <link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endsection
@section('page')
    Frais et commission sur transactions
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @if (hasPermission('admin.frais.add'))
                        <button type="button" class="btn waves-effect waves-light btn-primary" data-toggle="modal" data-target="#add-gamme">Ajouter des frais</button>
                        <br>
                        <br>
                    @endif
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Frais de transactions</h3>
                        </div>
    
                        <div class="box-body">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Type d'operation</th>
                                        <th>Intervalle</th>
                                        <th>Frais</th>
                                        <th>Commission Partenaire</th>
                                        @foreach ($compteCommissions as $item)
                                            <th>Commission {{$item->libelle}}</th>
                                        @endforeach
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($frais as $item)
                                        <tr>
                                            <td class="text-capitalize">{{ $item->type_operation }}</td>
                                            <td>{{ $item->start }} - {{ $item->end }} F CFA</td>
                                            <td>{{ $item->value }} @if($item->type == 'pourcentage') % @else F CFA @endif</td>
                                            <td>
                                                {{ $item->value_commission_partenaire }} @if($item->type_commission_partenaire == 'pourcentage') % @else F CFA @endif
                                            </td>
                                            @foreach ($compteCommissions as $val)
                                                @php $fee = \App\Models\FraiCompteCommission::where('frai_id',$item->id)->where('compte_commission_id',$val->id)->orderBy('created_at','DESC')->first() @endphp
                                                <td> {{$fee->value}} @if($fee->type == 'pourcentage') % @else F CFA @endif </td>                                                
                                            @endforeach
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
                                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#edit-frais-{{ $item->id }}"><i class="fa fa-edit"></i>&nbsp;&nbsp;Modifier les informations</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#del-frais-{{ $item->id }}"><i class="fa fa-trash"></i>&nbsp;&nbsp;Supprimer le frais</a>
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

        @foreach ($frais as $item)
            <div class="modal fade" id="edit-frais-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel1">Modification de frais </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <form action="/frais/edit/{{ $item->id }}" method="POST"> 
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="">Type d'operation</label>
                                    <select class="form-control select2bs4" required name="type_operation" style="width:100%">
                                        <option value="">Selectionner un type</option>
                                        <option @if($item->type_operation == "rechargement") selected @endif value="rechargement">Rechargement</option>
                                        <option @if($item->type_operation == "transfert") selected @endif value="transfert">Transfert</option>
                                        <option @if($item->type_operation == "depot") selected @endif value="depot">Depot chez partenaire</option>
                                        <option @if($item->type_operation == "retrait") selected @endif value="retrait">Retrait chez partenaire</option>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Montant debut</label>
                                            <input type="text" value="{{ $item->start }}" required class="form-control" name="debut" placeholder="Montant debut">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Montant fin</label>
                                            <input type="text" value="{{ $item->end }}" required class="form-control" name="fin" placeholder="Montant fin">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Type de taux</label>
                                            <select class="form-control select2bs4" required name="type" style="width:100%">
                                                <option value="">Selectionner un type</option>
                                                <option @if($item->type == "fixe") selected @endif  value="fixe">Taux fixe</option>
                                                <option @if($item->type == "pourcentage") selected @endif  value="pourcentage">Taux pourcentage</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Valeur des frais</label>
                                            <input type="text" value="{{ $item->value }}" required class="form-control" name="value" placeholder="Valeur">
                                        </div>
                                    </div>

                                    <hr>
                                    
                                    <h5>Partage de commission sur les frais</h5>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Type et valeur commission partenaire</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <select class="form-control select2bs4" required name="type_partenaire" style="width:100%">
                                                        <option value="">Selectionner un type</option>
                                                        <option @if($item->type_commission_partenaire == "fixe") selected @endif value="fixe">Taux fixe</option>
                                                        <option @if($item->type_commission_partenaire == "pourcentage") selected @endif value="pourcentage">Taux pourcentage</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="number" required value="{{ $item->value_commission_partenaire }}" class="form-control" name="value_partenaire" placeholder="Valeur">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @foreach ($compteCommissions as $val)
                                        @php $fee = \App\Models\FraiCompteCommission::where('frai_id',$item->id)->where('compte_commission_id',$val->id)->orderBy('created_at','DESC')->first() @endphp
                                         
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Type et valeur commission {{ $val->libelle }}</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <select class="form-control select2bs4" required name="type_{{ strtolower($val->libelle) }}" style="width:100%">
                                                            <option value="">Selectionner un type</option>
                                                            <option @if($fee->type == "fixe") selected @endif value="fixe">Taux fixe</option>
                                                            <option @if($fee->type == "pourcentage") selected @endif value="pourcentage">Taux pourcentage</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="number" value="{{ $fee->value }}" required class="form-control" name="value_{{ strtolower($val->libelle) }}" placeholder="Valeur">
                                                        <input type="hidden" value="{{ $val->id }}" class="form-control" name="id_{{ strtolower($val->libelle) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
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

            <div class="modal fade" id="del-frais-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel1">Suppression de frais </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <form action="/frais/delete/{{ $item->id }}" method="POST"> 
                            @csrf
                            <div class="modal-body">
                                <p>Cela implique que toutes opérations dans cet intervalle de montant sera exonérée de frais. <br> Etes vous sur de supprimer ce parametre ?</p>
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
    
        <div class="modal fade" id="add-gamme" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1">Definition des frais d'opérations</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="/frais/add" id="form-add-card-magasin" method="POST"> 
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="">Type d'operation</label>
                                <select class="form-control select2bs4" required name="type_operation" style="width:100%">
                                    <option value="">Selectionner un type</option>
                                    <option value="rechargement">Rechargement</option>
                                    <option value="transfert">Transfert</option>
                                    <option value="depot">Depot chez partenaire</option>
                                    <option value="retrait">Retrait chez partenaire</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Montant debut</label>
                                        <input type="text" required class="form-control" name="debut" placeholder="Montant debut">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Montant fin</label>
                                        <input type="text" required class="form-control" name="fin" placeholder="Montant fin">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Type de taux</label>
                                        <select class="form-control select2bs4" required name="type" style="width:100%">
                                            <option value="">Selectionner un type</option>
                                            <option value="fixe">Taux fixe</option>
                                            <option value="pourcentage">Taux pourcentage</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Valeur des frais</label>
                                        <input type="text" required class="form-control" name="value" placeholder="Valeur">
                                    </div>
                                </div>

                                <hr>
                                
                                <h5>Partage de commission sur les frais</h5>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Type et valeur commission partenaire</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select class="form-control select2bs4" required name="type_partenaire" style="width:100%">
                                                    <option value="">Selectionner un type</option>
                                                    <option value="fixe">Taux fixe</option>
                                                    <option selected value="pourcentage">Taux pourcentage</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="number" required value="0" class="form-control" name="value_partenaire" placeholder="Valeur">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @foreach ($compteCommissions as $item)
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Type et valeur commission {{ $item->libelle }}</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <select class="form-control select2bs4" required name="type_{{ strtolower($item->libelle) }}" style="width:100%">
                                                        <option value="">Selectionner un type</option>
                                                        <option value="fixe">Taux fixe</option>
                                                        <option selected value="pourcentage">Taux pourcentage</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="number" value="0" required class="form-control" name="value_{{ strtolower($item->libelle) }}" placeholder="Valeur">
                                                    <input type="hidden" value="{{ $item->id }}" class="form-control" name="id_{{ strtolower($item->libelle) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
@endsection

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
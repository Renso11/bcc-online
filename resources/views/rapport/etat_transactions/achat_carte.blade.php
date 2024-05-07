

<div class="box">
    <div class="box-body">
        <div class="row" style="padding:15px">
            <button type="button" class="btn btn-info exportToExcel">
                <i class="far fa-file-excel" aria-hidden="true"></i> Exporter en Excel
            </button>
            &nbsp;&nbsp;&nbsp;
            <a class="btn btn-info" href="/download/achat/cartes">
                <i class="far fa-file-pdf" aria-hidden="true"></i> Exporter en PDF
            </a>
        </div>   
        <br>
        <br>

        <div id="all-table">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr class="excel-header">
                            <th class="text-center"><span class="bold">Montant Opérations (XOF)</b></th>
                            <th class="text-center"><span class="bold">Total Opérations</b></th>
                        </tr>
                    </thead>
                    <tbody>        
                        <tr>
                            <td>{{ $sumBuys }}</td>
                            <td>{{ $nbBuys }}</td>
                        </tr>
                        <tr id="noExel" style="display:none">
                            <th colspan="2"></th>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br>  
            <br>  
            <div class="table-responsive">
                <table class="table table-bordered example1">
                    <thead>
                        <tr class="excel-header">
                            <th style="width:10%"><span class="bold">Date</b></th>
                            <th style="width:15%"><span class="bold">Client</b></th>
                            <th style="width:15%"><span class="bold">Montant</b></th>
                            <th style="width:5%"><span class="bold">Methode</b></th>
                            <th style="width:5%"><span class="bold">Status</b></th>
                            <th style="width:10%"><span class="bold">Reference Paiement</b></th>
                            <th style="width:10%"><span class="bold">Promo</b></th>
                            <th style="width:10%"><span class="bold">Partenaire</b></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($buys as $item)
                        <tr>
                            <td>{{ $item->date }}</td>
                            <td>{{ $item->userClient ? $item->userClient->name.' '.$item->userClient->lastname : '' }}</td>
                            <td>
                                {{ $item->montant }}
                            </td>
                            <td>{{ $item->moyen_paiement ? $item->moyen_paiement : '-' }}</td>
                            <td>{{ $item->status }}</td>
                            <td>{{ $item->reference_paiement }}</td>
                            <td> 
                                @if($item->partenaire)
                                    <span class="label label-success">Promo</span>
                                @else 
                                    <span class="label label-danger">Non-Promo</span>
                                @endif
                            </td>
                            <td> 
                                @if($item->partenaire)
                                    {{ $item->partenaire->libelle }}
                                @else 
                                    -
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
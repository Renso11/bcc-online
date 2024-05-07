
<div class="box">
    <div class="box-body">
        <div class="row" style="padding:15px">
            <button type="button" class="btn btn-info exportToExcel">
                <i class="far fa-file-excel" aria-hidden="true"></i> Exporter en Excel
            </button>
            &nbsp;&nbsp;&nbsp;
            <a class="btn btn-info" href="/download/transactions/client">
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
                            <th colspan="4" class="text-center"><span class="bold">Total Opérations</b></th>
                            <th colspan="4" class="text-center"><span class="bold">Montant Opérations (XOF)</b></th>
                            <th colspan="4" class="text-center"><span class="bold">Total frais (XOF)</b></th>
                        </tr>
                        <tr>
                            <th  class="text-center"><span class="bold">Depots</b></th>
                            <th  class="text-center"><span class="bold">Rechargement</b></th>
                            <th  class="text-center"><span class="bold">Transferts</b></th>
                            <th  class="text-center"><span class="bold">Retraits</b></th>
                            <th  class="text-center"><span class="bold">Depots</b></th>
                            <th  class="text-center"><span class="bold">Rechargement</b></th>
                            <th  class="text-center"><span class="bold">Transferts</b></th>
                            <th  class="text-center"><span class="bold">Retraits</b></th>
                            <th  class="text-center"><span class="bold">Depots</b></th>
                            <th  class="text-center"><span class="bold">Rechargement</b></th>
                            <th  class="text-center"><span class="bold">Transferts</b></th>
                            <th  class="text-center"><span class="bold">Retraits</b></th>
                        </tr>
                    </thead>
                    <tbody>        
                        <tr>
                            <td>{{ $statNb['depot'] }}</td>
                            <td>{{ $statNb['recharge'] }}</td>
                            <td>{{ $statNb['transfert'] }}</td>
                            <td>{{ $statNb['retrait'] }}</td>
                            <td>{{ $statSum['depot'] }}</td>
                            <td>{{ $statSum['recharge'] }}</td>
                            <td>{{ $statSum['transfert'] }}</td>
                            <td>{{ $statSum['retrait'] }}</td>
                            <td>{{ $statFrais['depot'] }}</td>
                            <td>{{ $statFrais['recharge'] }}</td>
                            <td>{{ $statFrais['transfert'] }}</td>
                            <td>{{ $statFrais['retrait'] }}</td>
                        </tr>
                        <tr id="noExel" style="display:none">
                            <th colspan="12"></th>
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
                            <th style="width:5%"><span class="bold">Type</b></th>
                            <th style="width:15%"><span class="bold">Client</b></th>
                            <th style="width:15%"><span class="bold">Partenaire</b></th>
                            <th style="width:5%"><span class="bold">Methode</b></th>
                            <th style="width:5%"><span class="bold">Status</b></th>
                            <th style="width:10%"><span class="bold">Montant</b></th>
                            <th style="width:10%"><span class="bold">Frais</b></th>
                            <th style="width:10%"><span class="bold">Reference Debit</b></th>
                            <th style="width:15%"><span class="bold">Reference Credit</b></th>
                        </tr>
                    </thead>
                    <tbody>        
                    @forelse($transactions as $item)
                    <tr>
                        <td>{{ $item['date'] }}</td>
                        <td>{{ $item['type'] }}</td>
                        <td>{{ array_key_exists('userClient',$item) ? $item['userClient']->name.' '.$item['userClient']->lastname : '' }}</td>
                        <td>{{ array_key_exists('partenaire',$item) ? $item['partenaire']['libelle'] : '' }}</td>
                        <td>{{ array_key_exists('moyen_paiement',$item) ? $item['moyen_paiement'] : '-' }}</td>
                        <td>{{ $item['status'] }}</td>
                        <td>{{ $item['montant'] }}</td>
                        <td>{{ $item['frais'] }}</td>
                        @if($item['type'] == 'Depot')
                            <td>
                                -
                            </td>
                            <td>
                                {{ array_key_exists('reference_gtp', $item) ? $item['reference_gtp'] : '-' }}
                            </td>
                        @elseif($item['type'] == 'Retrait')
                            <td>
                                {{ array_key_exists('reference_gtp', $item) ? $item['reference_gtp'] : '-' }}
                            </td>
                            <td>
                                -
                            </td>
                        @elseif($item['type'] == 'Recharge')
                            <td>{{ array_key_exists('reference_operateur',$item) ? $item['reference_operateur'] : '-' }}</td>
                            <td>
                                {{ array_key_exists('reference_gtp', $item) ? $item['reference_gtp'] : '-' }}
                            </td>
                        @else
                            <td>
                                {{ array_key_exists('reference_gtp', $item) ? $item['reference_gtp'] : '-' }}
                            </td>
                            <td>
                                {{ array_key_exists('reference_operateur',$item) ? $item['reference_operateur'] : '-' }}
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">Pas de données</td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
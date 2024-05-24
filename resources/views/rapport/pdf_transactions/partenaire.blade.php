<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE = edge">
    <meta name="viewport" content="width = device-width, initial-scale = 1.0">
    <title> Rapport des transactions partenaires </title>

    <style>
        @import url("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css");
        #watermark {
            position: fixed;

            /** 
                Set a position in the page for your image
                This should center it vertically
            **/
            bottom:   30%;
            left:     36%;

            /** Change image dimensions**/
            width:    8cm;
            height:   8cm;

            /** Your watermark should be behind every content**/
            z-index:  -1000;
            opacity: 0.4;
        }
    </style>
</head>

<body>
    <div id="watermark">
        <img src="{{ asset('/img/bcb.png') }}" height="100%" width="100%" />
    </div>
    <main>
        <h2> Transactions des partenaires </h2>  
        <div id="all-table">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr class="excel-header">
                            <th colspan="6" class="text-center"><span class="bold">Total Opérations</b></th>
                            <th colspan="6" class="text-center"><span class="bold">Montant Opérations (XOF)</b></th>
                            <th colspan="6" class="text-center"><span class="bold">Total frais (XOF)</b></th>
                        </tr>
                        <tr>
                            <th  class="text-center"><span class="bold">Depot</b></th>
                            <th  class="text-center"><span class="bold">Retraits</b></th>
                            <th  class="text-center"><span class="bold">Appro</b></th>
                            <th  class="text-center"><span class="bold">Recharge</b></th>
                            <th  class="text-center"><span class="bold">Cashout</b></th>
                            <th  class="text-center"><span class="bold">Cession</b></th>
                            <th  class="text-center"><span class="bold">Depot</b></th>
                            <th  class="text-center"><span class="bold">Retraits</b></th>
                            <th  class="text-center"><span class="bold">Appro</b></th>
                            <th  class="text-center"><span class="bold">Recharge</b></th>
                            <th  class="text-center"><span class="bold">Cashout</b></th>
                            <th  class="text-center"><span class="bold">Cession</b></th>
                            <th  class="text-center"><span class="bold">Depot</b></th>
                            <th  class="text-center"><span class="bold">Retraits</b></th>
                            <th  class="text-center"><span class="bold">Appro</b></th>
                            <th  class="text-center"><span class="bold">Recharge</b></th>
                            <th  class="text-center"><span class="bold">Cashout</b></th>
                            <th  class="text-center"><span class="bold">Cession</b></th>
                        </tr>
                    </thead>
                    <tbody>        
                        <tr>
                            <td>{{ $statNb['depot'] }}</td>
                            <td>{{ $statNb['retrait'] }}</td>
                            <td>{{ $statNb['approvisionnement'] }}</td>
                            <td>{{ $statNb['recharge'] }}</td>
                            <td>{{ $statNb['cashout'] }}</td>
                            <td>{{ $statNb['cession'] }}</td>
                            
                            <td>{{ $statSum['depot'] }}</td>
                            <td>{{ $statSum['retrait'] }}</td>
                            <td>{{ $statSum['approvisionnement'] }}</td>
                            <td>{{ $statSum['recharge'] }}</td>
                            <td>{{ $statSum['cashout'] }}</td>
                            <td>{{ $statSum['cession'] }}</td>
                            
                            <td>{{ $statFrais['depot'] }}</td>
                            <td>{{ $statFrais['retrait'] }}</td>
                            <td>{{ $statFrais['approvisionnement'] }}</td>
                            <td>{{ $statFrais['recharge'] }}</td>
                            <td>{{ $statFrais['cashout'] }}</td>
                            <td>{{ $statFrais['cession'] }}</td>
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
                            <th style="width:15%"><span class="bold">Carte</b></th>
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
                        <td>{{ array_key_exists('userClient',$item) && $item['userClient'] ? $item['userClient']->name.' '.$item['userClient']->lastname: '' }}</td>
                        <td>{{ array_key_exists('userCard',$item) && $item['userCard'] ? decryptData((string)$item['userCard']->customer_id,env('ENCRYPT_KEY')).', ***'.decryptData((string)$item['userCard']->last_digits,env('ENCRYPT_KEY')) : '' }}</td>
                        <td>{{ array_key_exists('partenaire',$item) ? $item['partenaire']['libelle'] : '' }}</td>
                        <td>{{ array_key_exists('moyen_paiement',$item) ? $item['moyen_paiement'] : '-' }}</td>
                        <td>{{ $item['status'] }}</td>
                        <td>{{ $item['montant'] }}</td>
                        <td>{{ array_key_exists('frais',$item) ? $item['frais'] : '-'}}</td>
                        @if($item['type'] == 'Depot')
                            <td>
                                {{ array_key_exists('reference_gtp', $item) ? $item['reference_gtp'] : '-' }}
                            </td>
                            <td>
                                -
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
                        @elseif($item['type'] == 'Cashout')
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
    </main>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KinkN" crossorigin="anonymous">
    </script>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>

</body>

</html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE = edge">
    <meta name="viewport" content="width = device-width, initial-scale = 1.0">
    <title> Rapport des achats de carte en ligne </title>

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
        <h2> Achat de carte en ligne </h2>  
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
                                    Promo
                                @else 
                                    Non-Promo
                                @endif
                            </td>
                        </tr>
                    @endforeach
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
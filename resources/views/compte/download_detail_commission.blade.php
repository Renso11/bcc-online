<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE = edge">
    <meta name="viewport" content="width = device-width, initial-scale = 1.0">
    <title> Rapport des commissions de {{ $compteCommission->libelle }} </title>

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
        <h2> Commission de {{ $compteCommission->libelle }} </h2>  
        <div id="all-table">
            <div class="table-responsive">
                <table class="table table-bordered table-striped example1">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference GTP</th>
                            <th class="text-capitalize">Type</th>
                            <th>Montant <small>(F cfa)</small></th>
                            <th>Frais <small>(F cfa)</small></th>
                            <th>Commission <small>(F cfa)</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($compteCommissionOperations as $item)
                            <tr>
                                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                <td>{{ $item->reference_gtp }}</td>
                                <td class="text-capitalize">{{ $item->type_operation }}</td>
                                <td>{{ $item->montant }}</td>
                                <td>{{ $item->frais }}</td>
                                <td>{{ $item->commission }}</td>
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

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

    @if ($payment == null) 
        <div class="container" style="padding-top: 25%">       
            <div class="alert alert-danger" role="alert">
                <h1 class="text-center">Transaction introuvable</h1>
            </div>
        </div>
    @elseif ($payment->status == 'completed')  
        <div class="container" style="padding-top: 25%">      
            <div class="alert alert-success" role="alert">
                Paiement effectué
            </div>        
        </div>        
    @else
        <form action="/validation/transaction/kkiapay/{{$payment->id}}" id="form-success" method="post">
            @csrf
            <input type="hidden" autocomplete="off" required name="transaction_id" id="reference">
        </form>
        <form action="/rejet/transaction/kkiapay/{{$payment->id}}" id="form-error" method="post">
            @csrf
        </form>
        
        <script src="/bower_components/jquery/dist/jquery.min.js"></script>

        <script src="/bower_components/jquery-ui/jquery-ui.min.js"></script>

        <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="https://cdn.kkiapay.me/k.js"></script>
        <script>
            $(document).ready(function() { 
                
                var montant = {{(int)$payment->montant}};
                openKkiapayWidget({
                    amount: montant,
                    position: "center",
                    sandbox: @if(env('KKIAPAY_SANDBOX') == 1)true @else false @endif,
                    theme: "#975102",
                    key: "{{ env('API_KEY_KKIAPAY')}}",
                    callback:"",
                    name:"{{$payment->userPartenaire->name.' '.$payment->userPartenaire->lastname}}",
                    email:"{{$payment->partenaire->email}}",
                    phone:"{{$payment->wallet->phone}}",
                })
                addSuccessListener(response => {
                    $('#reference').val(response.transactionId)
                    $('#form-success').submit();
                })
                addFailedListener(error => {
                    $('#form-error').submit();
                });

             });
        </script>
    @endif

</body>
</html>
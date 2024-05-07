@extends('base')
@section('css')
<link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
@endsection
@section('page')
    Liste des operations partenaires annulees
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <br>
                    <br>
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Liste des operations partenaires en annulées </h3>
                        </div>

                        <div class="box-body">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th style="width:15%">Date</th>
                                        <th style="width:15%">Client</th>
                                        <th style="width:10%">Type</th>
                                        <th style="width:10%">Moyen</th>
                                        <th style="width:10%">Montant</th>
                                        <th style="width:20%">Motif d'annulation</th>
                                    </tr>
                                </thead>
                                <tbody>        
                                    @foreach($transactions as $item)
                                        <tr>
                                            <td>{{ $item['date'] }}</td>
                                            <td>{{ $item['userClient'] ? $item['userClient']->name.' '.$item['userClient']->lastname : '' }}</td>
                                            <td>{{ $item['type'] }}</td>
                                            <td>{{ $item['moyen_paiement'] }}</td>
                                            <td>{{ $item['montant'] }}</td>
                                            <td>{{ $item['cancel_motif'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
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
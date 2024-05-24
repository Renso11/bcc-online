@extends('base')
@section('css')
    <link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endsection
@section('content')
    <section class="content">
        <br>
        <div class="row">
            <div class="col-lg-12 col-xs-12">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>{{ $apporteur->solde_commission }}</h3>
                        <p>Compte commission de  {{ $apporteur->lastname.' '.$apporteur->name }}</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Operations de l'apporteur  {{ $apporteur->lastname.' '.$apporteur->name }}</h3>
                    </div>
    
                    <div class="box-body">
                        <br>
                        <br>
                        <div id="resultat">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Libelle</th>
                                        <th>Montant</th>
                                        <th>Sens</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($operations as $item)
                                        <tr>
                                            <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                                            <td>{{ $item->libelle }}</td>
                                            <td>{{ $item->montant }}</td>
                                            <td class="text-capitalize">{{ $item->sens }}</td>
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
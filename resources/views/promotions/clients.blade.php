@extends('base')
@section('css')
<link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
@endsection
@section('page')
    Liste des promotions clients
@endsection
@section('title')
    Liste des promotions clients
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Liste des promotions </h3>
                        </div>

                        <div class="box-body">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Operation</th>
                                        <th>type</th>
                                        <th>Code promo</th>
                                        <th>Date debut</th>
                                        <th>Date fin</th>
                                        <th>Gain client</th>
                                        <th>Gain partenaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($promotions as $item)
                                        <tr>
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
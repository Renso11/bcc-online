@extends('base')
@section('css')
<link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
@endsection
@section('page')
    Liste des comptes clients rejetes
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Liste des compte clients rejetes </h3>
                        </div>
    
                        <div class="box-body">
                            <table class="table table-bordered table-striped example1">
                                <thead>
                                    <tr>
                                        <th>Nom et prénoms</th>
                                        <th>Telephone</th>
                                        <th>Status</th>
                                        <th>Motif du rejet</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userClients as $item)
                                        <tr>
                                            <td>{{ $item->lastname.' '.$item->name }}</td>
                                            <td>{{ $item->username }}</td>
                                            <td><span class="label label-danger">Rejeté</span></td>
                                            <td>{{$item->motif_rejet}}</td>
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
    <script>
        function formatState (state) {
            if (!state.id) { 
                return state.text; 
            }
            var $state = $(
                '<span><img src="/assets/images/flags/' +  state.element.dataset.flag.toLowerCase() +
                '.svg" class="img-flag" /> ' +
                state.text +  '</span>'
            );
            return $state;
        };

        $(".select2-flag-search").select2({
            templateResult: formatState,
            templateSelection: formatState,
            escapeMarkup: function(m) { return m; },
            width: '100%',
            dropdownParent: $("#add-client")
        });

        $('#code').on('keyup',function (e) {
            var code = $(this).val();
            $('#name').val("")
            $('#lastname').val("")
            $('#last').val("")
            if(code.length >= 8){
                $('#loader').show()
                $.ajax({
                    url: "/search/client",
                    data: {
                        code : code
                    },
                    method: "post",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data){
                        let res = JSON.parse(data)
                        $('#loader').hide()
                        $('#name').val(res.firstName)
                        $('#lastname').val(res.lastname)
                        $('#last').val(res.lastFourDigits)
                    }
                }); 
            }
        })
        
    </script>
@endsection
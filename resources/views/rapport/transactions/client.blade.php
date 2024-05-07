@extends('base')
@section('css')
    <link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    
@endsection
@section('page')
    Rapport des transactions par clients
@endsection
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Transactions par clients </h3>
                    </div>
    
                    <div class="box-body">
                        <form id="form-search">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="">Type d'operation</label>
                                    <select class="form-control select2bs4" id="type_operation" multiple name="type_operations[]" style="width:100%">
                                        <option value="all">Tous</option>
                                        <option value="depot">Depot</option>
                                        <option value="retrait">Retrait</option>
                                        <option value="rechargement">Rechargement</option>
                                        <option value="transfert"> Transfert</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="">Status de l'operation</label>
                                    <select class="form-control select2bs4" id="status" multiple name="status[]" style="width:100%">
                                        <option value="all">Tous</option>
                                        <option value="pending">En cours</option>
                                        <option value="completed">Finalisée</option>
                                        <option value="refunded"> Rembourser </option>
                                        <option value="cancelled">Annuler</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="">Debut</label>
                                    <input type="datetime-local" class="form-control" id="debut" name="debut">
                                </div>
                                <div class="col-md-3">
                                    <label for="">Fin</label>
                                    <input type="datetime-local" class="form-control" id="fin" name="fin">
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                </div>
                                <div class="col-md-4">
                                    <button id="btn-search" class="btn btn-info" style="width: 100%">
                                        <i class="fa fa-search"></i> Rechercher
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12" id="resultat">
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
    <script src="/excel/src/jquery.table2excel.js"></script> 
    <script>
        $(".example1").DataTable({
            ordering: false
        });
        
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })


        
        $('#btn-search').on('click',function(e) {
            e.preventDefault()
            var $this = $(this)
            $this.prop('disabled',true)
            $('#resultat').hide()
            $this.html('<i class="fa fa-spinner fa-spin"></i> Recherche...')
            var formData = new FormData($('#form-search')[0]);
            var type_operation  = $('#type_operation').val();
            var status = $('#status').val();            

            if(type_operation == "" || status == ""){
                toastr.warning("Veuillez renseigner le type d'operation et/ou le status")
                $this.html(`<i class="fa fa-search"></i> Rechercher</button>`)
                $this.prop('disabled',false)
            }else{
                $.ajax({
                    url: "/search/transactions/client",
                    data: formData,
                    processData: false,
                    contentType: false,
                    method: "post",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data){
                        $('#resultat').html(data)
                        $('#resultat').show()
                        $this.html(`<i class="fa fa-search"></i> Rechercher</button>`)
                        $this.prop('disabled',false)
                        
                        $(".example1").DataTable({
                            ordering: false
                        });
                        
                        $(".exportToExcel").click(function (e) {
                            $(".example1").DataTable().destroy() 
                            var $tableClone = $("#all-table").clone();
                            $('#noExel').show();
                            $('.bold').css("font-weight", "bold");

                            $($tableClone).table2excel({
                                exclude: ".noExl",
                                name: "Rapport des transactions des clients",
                                filename: "Rapport des transactions des clients.xls",
                                fileext: ".xls",
                                exclude_img: true,
                                exclude_links: true,
                                exclude_inputs: true,
                                preserveColors: true
                            });
                            $('#noExel').hide();
                            $(".example1").DataTable({
                                ordering: false
                            });
                            $('.bold').removeAttr('style');
                        });
                    }
                }); 
            }
        })
    </script>
@endsection
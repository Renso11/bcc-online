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
                        <h3>{{ $compteCommission->solde }}</h3>
                        <p>Compte commission {{ $compteCommission->libelle }}</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="#" data-target="#modal-depot" class="small-box-footer" data-toggle="modal"> <i class="fa fa-minus"></i> &nbsp; Reverser la commission</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Mouvement du compte de commission </h3>
                    </div>
    
                    <div class="box-body">
                        <form id="form-search">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">Debut</label>
                                    <input type="datetime-local" class="form-control" id="debut" name="debut">
                                </div>
                                <div class="col-md-6">
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
                        <br>
                        <br>
                        <div id="resultat">
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
            var debut  = $('#debut').val();
            var fin = $('#fin').val();            

            if(debut == "" && fin == ""){
                toastr.warning("Veuillez renseigner la periode de votre recherche")
                $this.html(`<i class="fa fa-search"></i> Rechercher</button>`)
                $this.prop('disabled',false)
            }else{
                $.ajax({
                    url: "/search/mouvement/compte/{{$compteCommission->id}}",
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
                                name: "Rapport des commissions de {{ $compteCommission->libelle }}",
                                filename: "Rapport des commissions de {{ $compteCommission->libelle }}.xls",
                                fileext: ".xls",
                                exclude_img: true,
                                exclude_links: true,
                                exclude_inputs: true,
                                preserveColors: true
                            });
                            
                            console.log('o,')
                            
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
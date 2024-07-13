
<div class="row" style="padding:15px">
    <button type="button" class="btn btn-info exportToExcel">
        <i class="far fa-file-excel" aria-hidden="true"></i> Exporter en Excel
    </button>
    &nbsp;&nbsp;&nbsp;
    <a class="btn btn-info" href="/download/mouvement/compte/{{$compteCommission->id}}/{{$debut}}/{{$fin}}">
        <i class="far fa-file-pdf" aria-hidden="true"></i> Exporter en PDdF
    </a>
</div>   
<br>
<br>
<table class="table table-bordered table-striped example1" id="all-table">
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
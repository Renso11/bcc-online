
<div class="content">
    <h4 class="text-center"> Revelé de <b>{{ $client->name.' '.$client->lastname }}</b> du <b>{{ $debut }}</b> au <b>{{ $fin }}</b></h4>  
    <br>
    <div id="all-table">
        <div class="table-responsive">
            <table class="table table-bordered" id="example2">
                <thead>
                    <tr class="excel-header">
                        <th style="width:10%"><span class="bold">Date</b></th>
                        <th style="width:10%"><span class="bold">Type</b></th>
                        <th style="width:30%"><span class="bold">Libelle</b></th>
                        <th style="width:10%"><span class="bold">Montant</b></th>
                        <th style="width:5%"><span class="bold">Frais</b></th>
                    </tr>
                </thead>
                <tbody>        
                    @foreach($transactions as $item)
                        <tr>
                            <td>{{ $item->created_at }}</td>
                            <td class="text-capitalized">{{ $item->type }}</td>
                            <td>{{ $item->libelle }}</td>
                            <td>{{ $item->montant }}</td>
                            <td>{{ $item->frais }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
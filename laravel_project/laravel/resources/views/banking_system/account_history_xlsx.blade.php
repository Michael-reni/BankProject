<table>
    <thead>
        <tr>
            <th>Account Number: {{$data[0]['account_number']}}</th>
           
        </tr>
        <tr>
            <th>Operation type</th>
            <th>Amount</th>
            <th>Operation date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $d)
        <tr>
            <td>{{$d['operation_type']}}</td>
            <td>{{$d['amount']}}</td>
            <td>{{$d['created_at']}}</td>
        </tr>
        @endforeach
       

    </tbody>

</table>
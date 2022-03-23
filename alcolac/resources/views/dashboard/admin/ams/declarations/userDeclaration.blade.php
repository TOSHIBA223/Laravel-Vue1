
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="/js/tablefilter/tablefilter.js"></script>
    <table class="table table-responsive-sm table-striped filtered-table  filtered-table-declaration">
        <thead class="text">
        <tr>
            <th>Status</th>
            <th>Self Contact</th>
            <th>Symptoms</th>
            <th>Living With Contact</th>
            <th>Passed</th>
            <th>Date Sent</th>
        </tr>
        </thead>
        <tbody>
        @if(empty($declarations))
            <tr>
                <td colspan="6" class="text-center">No Records Found</td>
            </tr>
        @else
            @foreach($declarations as $dec)

                <tr>
                    <td class="{{ $dec['status_text']}}">{{ $dec['status'] }}</td>
                    <td class="{{ $dec['self_contact'] == 'no' ? 'text-success' : 'text-danger'}}">{{ $dec['self_contact'] }}</td>
                    <td class="{{ $dec['symptoms'] == 'no' ? 'text-success' : 'text-danger'}}">{{ $dec['symptoms'] }}</td>
                    <td class="{{ $dec['living'] == 'no' ? 'text-success' : 'text-danger'}}">{{ $dec['living'] }}</td>
                    <td class="{{ $dec['passed'] == 'Passed' ? 'text-success' : 'text-danger'}}">{{ $dec['passed'] }}</td>
                    <td>{{ $dec['created_at'] }}</td>
                </tr>
            @endforeach
            <script src="/js/declarationFilterDeclarations.js"></script>
        @endif
        </tbody>
    </table>


@section('javascript')

@endsection


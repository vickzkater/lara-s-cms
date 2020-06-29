<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Title</th>
        <th>Subtitle</th>
        <th>Description</th>
        <th>Created</th>
        <th>Last Updated</th>
    </tr>
    </thead>
    <tbody>
        @php
            $i = 1;
        @endphp
        @foreach($data as $item)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->subtitle }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->updated_at }}</td>
            </tr>
            @php
                $i++;
            @endphp
        @endforeach
    </tbody>
</table>
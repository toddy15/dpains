@extends('app')

@section('content')
    <table class="table table-striped">
        <thead>
            <th>Mitarbeitergruppe</th>
            <th>Reihenfolge</th>
            <th>Aktion</th>
        </thead>
        <tbody>
            @foreach($staffgroups as $staffgroup)
                <tr>
                    <td>{{ $staffgroup->staffgroup }}</td>
                    <td>{{ $staffgroup->weight }}</td>
                    <td><a class="btn btn-primary btn-default" href="{{ action('StaffgroupController@edit', $staffgroup->id) }}">Bearbeiten</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

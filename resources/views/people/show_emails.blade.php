@extends('app')

@section('content')
    @if ($email_missing)
        <h1>Fehlende E-Mail-Adressen</h1>
        <table class="table table-striped">
            <thead>
            <th>Name</th>
            <th>E-Mail</th>
            <th>Aktion</th>
            </thead>
            <tbody>
            @foreach($email_missing as $person)
                <tr>
                    <td>{{ $person->name }}</td>
                    <td>{{ $person->email }}</td>
                    <td>
                        <a class="btn btn-primary"
                           href="{{ action('PersonInfoController@edit', $person->number) }}">Bearbeiten</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    <h1>E-Mail-Adressen der Mitarbeiter</h1>
    <table class="table table-striped">
        <thead>
        <th>Name</th>
        <th>E-Mail</th>
        <th>Aktion</th>
        </thead>
        <tbody>
        @foreach($email_complete as $person)
            <tr>
                <td>{{ $person->name }}</td>
                <td>{{ $person->email }}</td>
                <td>
                    <a class="btn btn-primary"
                       href="{{ action('PersonInfoController@edit', $person->number) }}">Bearbeiten</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

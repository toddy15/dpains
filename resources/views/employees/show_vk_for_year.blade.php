@extends('app')

@section('content')
    <h1>Übersicht der VK für {{ $year }}</h1>
    <h2>Mitarbeiter</h2>
    <table class="table table-striped">
        <thead>
            <th>Name</th>
            <th>Gruppe</th>
            <th>Jan</th>
            <th>Feb</th>
            <th>Mär</th>
            <th>Apr</th>
            <th>Mai</th>
            <th>Jun</th>
            <th>Jul</th>
            <th>Aug</th>
            <th>Sep</th>
            <th>Okt</th>
            <th>Nov</th>
            <th>Dez</th>
        </thead>
        <tbody>
        @foreach($employees as $employee)
            <tr>
                <td>{{ $employee['name'] }}</td>
                <td>{{ $employee['staffgroup'] }}</td>
                @for ($month = 1; $month <= 12; $month++)
                    <td>{{ $employee['months'][$month]['vk'] }}</td>
                @endfor
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

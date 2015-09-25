@extends('app')
@inject('helper', 'App\Dpains\Helper')

@section('content')
    <h1>Auswertung für {{ $year }}</h1>
    @if ($readable_worked_month)
        <h2>Gearbeitet bis Ende {{ $readable_worked_month }}, geplant bis Ende {{ $readable_planned_month }}</h2>
    @else
        <h2>Geplant bis Ende {{ $readable_planned_month }}</h2>
    @endif
    @foreach($tables as $staffgroup => $table)
        <h3>{{ $staffgroup }}</h3>
        <table class="table table-striped">
            <thead>
                <th>{!! $helper->sortTableBy('name', 'Name', $year) !!}</th>
                <th>{!! $helper->sortTableBy('worked_nights', 'Gearb. Nächte', $year) !!}</th>
                <th>{!! $helper->sortTableBy('planned_nights', 'Gepl. Nächte', $year) !!}</th>
                <th>{!! $helper->sortTableBy('diff_planned_nights', 'Abweichung', $year) !!}</th>
                <th>{!! $helper->sortTableBy('worked_nefs', 'Gearb. NEF', $year) !!}</th>
                <th>{!! $helper->sortTableBy('planned_nefs', 'Gepl. NEF', $year) !!}</th>
                <th>{!! $helper->sortTableBy('diff_planned_nefs', 'Abweichung', $year) !!}</th>
            </thead>
            <tbody>
                @foreach($table as $rows)
                    @foreach($rows as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->worked_nights }}</td>
                            <td>{{ $row->planned_nights }}</td>
                            <td>{{ $row->diff_planned_nights }}</td>
                            <td>{{ $row->worked_nefs }}</td>
                            <td>{{ $row->planned_nefs }}</td>
                            <td>{{ $row->diff_planned_nefs }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endforeach
@endsection
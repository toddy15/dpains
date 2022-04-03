@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Passwort zurücksetzen</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group {{ $errors->has('email') ? 'has-error has-feedback' : '' }}">
                                <label class="col-md-4 form-label">E-Mail-Adresse</label>

                                <div class="col-md-6">
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                                    @if ($errors->has('email'))
                                        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('password') ? 'has-error has-feedback' : '' }}">
                                <label class="col-md-4 form-label">Passwort</label>

                                <div class="col-md-6">
                                    <input type="password" class="form-control" name="password">
                                    @if ($errors->has('password'))
                                        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error has-feedback' : '' }}">
                                <label class="col-md-4 form-label">Passwort bestätigen</label>

                                <div class="col-md-6">
                                    <input type="password" class="form-control" name="password_confirmation">
                                    @if ($errors->has('password_confirmation'))
                                        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Passwort zurücksetzen
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

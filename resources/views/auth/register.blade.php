@extends('app')

@section('content')
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-default">
					<div class="panel-heading">Registrieren</div>
					<div class="panel-body">
						<form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/register') }}">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">

							<div class="form-group {{ $errors->has('name') ? 'has-error has-feedback' : '' }}">
								<label class="col-md-4 control-label">Name</label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="name" value="{{ old('name') }}">
									@if ($errors->has('name'))
										<span class="glyphicon glyphicon-remove form-control-feedback"></span>
									@endif
								</div>
							</div>

							<div class="form-group {{ $errors->has('email') ? 'has-error has-feedback' : '' }}">
								<label class="col-md-4 control-label">E-Mail-Adresse</label>
								<div class="col-md-6">
									<input type="email" class="form-control" name="email" value="{{ old('email') }}">
									@if ($errors->has('email'))
										<span class="glyphicon glyphicon-remove form-control-feedback"></span>
									@endif
								</div>
							</div>

							<div class="form-group {{ $errors->has('password') ? 'has-error has-feedback' : '' }}">
								<label class="col-md-4 control-label">Passwort</label>
								<div class="col-md-6">
									<input type="password" class="form-control" name="password">
									@if ($errors->has('password'))
										<span class="glyphicon glyphicon-remove form-control-feedback"></span>
									@endif
								</div>
							</div>

							<div class="form-group {{ $errors->has('password_confirmation') ? 'has-error has-feedback' : '' }}">
								<label class="col-md-4 control-label">Passwort bestätigen</label>
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
										Registrieren
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

					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group row">
							<label for="firstname" class="col-md-4 text-md-right">{{ __('First Name') }}</label>
							<div class="col-md-6" id="firstname">
                                <strong>{{ old('firstname', $userdetails->firstname ) }}</strong>
                            </div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group row">
							<label for="lastname" class="col-md-4 text-md-right">{{ __('Last Name') }}</label>
							<div class="col-md-6" id="lastname">
                                <strong>{{ old('lastname', $userdetails->lastname ) }}</strong>
                            </div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group row">
							<label for="email" class="col-md-4 text-md-right fa fa-envelope" data-toggle="tooltip" title="Email"></label>
							<div class="col-md-6" id="email">
                                <strong>{{ old('email', $userdetails->email ) }}</strong>
                            </div>
						</div>
					</div>

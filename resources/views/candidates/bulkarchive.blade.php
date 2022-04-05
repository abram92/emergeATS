@extends('layouts.admin')

@section('content')
<div class="container col-md-12">
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-md-12">
					<h3>{{ __('Bulk Archive Candidates') }}</h3>
				</div>
			</div>
		</div>
		<div class="card-body">
			
@include('partials.flashmessages')

                                <form class="form-horizontal" role="form" method="post">
                    @csrf

               <div class="form-group">
				<div class="row search-dates">
@include('partials.filter_daterange', ['fieldlabel'=>'Archive Date', 
									'fieldname_from'=>'archive_from', 
									'fieldvalue_from'=>(isset($q['archive_from'])) ? $q['archive_from']:null,
									'fieldname_to'=>'archive_to', 
									'fieldname_to_required' => true,
									'fieldvalue_to'=>(isset($q['archive_to'])) ? $q['archive_to']:null])				
				</div>				   

                      </div>
                      <hr>
                      <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary" onclick="confirm('Are you sure?')">Archive</button>
                      </div>
					  </form>
			
		</div>
	</div>	
</div>

@endsection 
		
@push('scripts')
@include('scripts.src_select2')
@endpush		

@section('js')


    <script>
	
		$("document").ready(function() {
			
		@include('scripts.ready_select2')
					
		});
		
		
    </script>
@endsection
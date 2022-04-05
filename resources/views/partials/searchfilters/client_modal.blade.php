
<div class="input-group-btn">
	<div class="btn-group" role="group">
@if (isset($query) && isset($query['search_id']))
     <a class="btn" id="savebutton" href="{{ route('savedsearch.edit',$query['search_id']) }}" target="savesearch{{ $query['search_id'] }}">Save Search</a>
@endif	
		<div class="dropdown dropdown-sm">
			<button type="button" class="btn btn-navbar dropdown-toggle dropdown-toggle-split" data-toggle="modal" data-target="#searchform" id="searchbutton">
				<i class="fas fa-search"></i>
			</button>  
		</div>
	</div>
</div>	
		
<div class="modal fade " id="searchform" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Client Search Filter</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			
            <form class="form-horizontal bg-white" method="post" role="form" id="clientresults" target="clientresults" action="{{ url('clients/search') }}">
				<div class="modal-body">@csrf
					<div class="card-deck">
						<div class="card card-body">
@include('partials.filter_text_input', ['fieldname'=>'q[name]', 'fieldlabel'=>'Client Name', 
									'fieldplaceholder'=>'Filter Client Name', 
									'fieldvalue'=>(isset($q['name'])) ? $q['name']:''])

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[consultants][]', 'fieldlabel'=>'Consultants', 
									'fieldplaceholder'=>'Select Consultants', 
									'options'=>$allconsultants,
									'selectedoptions'=>old('q[consultants]', isset($q['consultants']) ? $q['consultants'] : null)])

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[statuses][]', 'fieldlabel'=>'Statuses', 
									'fieldplaceholder'=>'Choose Statuses', 
									'options'=>$allstatuses,
									'selectedoptions'=>old('q[statuses]', isset($q['statuses']) ? $q['statuses'] : null)])
						</div>
						
						<div class="card col-md-6">
							<div class="card-header">Contact</div>
							<div class="card-body">
@include('partials.filter_text_input', ['fieldname'=>'q[contact][name]', 'fieldlabel'=>'Contact Name', 
									'fieldplaceholder'=>'Filter Contact Name', 
									'fieldvalue'=>(isset($q['contact']['name'])) ? $q['contact']['name']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[contact][position]', 'fieldlabel'=>'Contact Job Title', 
									'fieldplaceholder'=>'Filter Job Title', 
									'fieldvalue'=>(isset($q['contact']['position'])) ? $q['contact']['position']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[contact][phone]', 'fieldlabel'=>'Contact Telephone or Mobile', 
									'fieldplaceholder'=>'Filter Telephone or Mobile', 
									'fieldvalue'=>(isset($q['contact']['phone'])) ? $q['contact']['phone']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[contact][email]', 'fieldlabel'=>'Contact Email', 
									'fieldplaceholder'=>'Filter Email', 
									'fieldvalue'=>(isset($q['contact']['email'])) ? $q['contact']['email']:''])
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-body">
@include('partials.filter_longtext_note')


@include('partials.filter_text_input', ['fieldname'=>'q[techenvironment]', 'fieldlabel'=>'Tech Environment', 
									'fieldplaceholder'=>'Filter Tech Environment', 
									'fieldvalue'=>(isset($q['techenvironment'])) ? $q['techenvironment']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[agencynotes]', 'fieldlabel'=>'Agency Notes', 
									'fieldplaceholder'=>'Filter Agency Notes', 
									'fieldvalue'=>(isset($q['agencynotes'])) ? $q['agencynotes']:''])
						</div>
					</div>
				</div>	
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary fas fa-search" >Search</button>
				</div>
			</form>
        </div>
    </div>
</div>

@push('scripts')
@include('scripts.src_select2')
@endpush

@section('js')
@parent

    <script>
	
		$("document").ready(function() {
			
			
		@include('scripts.ready_select2')
					
		});
		
		
    </script>
@stop
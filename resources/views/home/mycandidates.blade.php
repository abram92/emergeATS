

@if (count($data) > 0)

<div class="mt-5 candidate-container mb-5 container">
    <div class="row pt-4 mb-4">
		<div class="text-left col-sm-6 col-md-5 col-lg-5 col-6">
			<label class="font-weight-medium mycandidatesize">My Candidtates</label></div>
			<div class="text-right col-sm-6 col-md-3 col-lg-3 offset-md-4 offset-lg-4 col-6">


                    <a href="{{ route('candidates.create') }}" data-toggle="tooltip" title="Add Candidate" target="_blank" class="add-new-btn">@include('partials.icons.add_candidate')
                    <label class="font-weight-normal" role="button" tabindex="0">
                    Add new</label>
                    </a>
					<!----></div></div>
                    <div class="row pb-4"><div class="col-lg-3 offset-lg-9"><div class="search"><div class="row align-items-center"><div class="text-center col-sm-2 col-md-1 col-lg-1 col-1"><svg viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="search" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-search b-icon bi"><g><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path></g></svg></div><div class="text-left col-sm-9 col-md-10 col-lg-10 col-10"><input type="text" placeholder="Search list" class="search-field"></div></div></div></div></div>


	<div class="table-responsive">
	<table class="table table-bordered datatable">
	<thead class="table-dark">
	<tr>
	<th>Name</th>
	{{-- <th>Contact Details</th> --}}
	<th>Consultant</th>
	<th>Status</th>
	<th>Date Activated</th>
	<th style="width:200px;">Actions</th>
	</tr>
	</thead>
	<tbody>
		@foreach ($data as $key => $candidate)
	<tr>
	<td>@include('partials.candidates.newavatar_img')
        {{-- {{ $candidate->user->listname }} --}}
        @include('partials.newcontact_view_grouped2', ['contacts'=>$candidate->contactfields])

    </td>
	{{-- <td>				@include('partials.contact_view_grouped2', ['contacts'=>$candidate->contactfields])
</td> --}}
	<td>@include('partials.list_consultant',['cons'=>$candidate->consultant])</td>
	<td>@include('partials.show_status', ['status'=>$candidate->status])</td>
	<td>@include('partials.list_date_format',['dt'=>$candidate->activated_at])</td>
	<td>
				@include('partials.list_candidate_actions', ['dropdownAlign'=>'dropdown-menu-right'])

</td>
	</tr>
		@endforeach
	</tbody>
	<tfoot>
	</tfoot>
	</table>
	</div>
	@include('partials.show_pagination')

</div>

@endif



<div class="mt-5 candidate-container mb-5 p-0 container">
         <section class="cover-image w-100">
            <div class="row p-0 mb-0">
              <div class="col-12 text-left p-0">
                <div class="cover-div">
                  <div class="cover-profile-pic">  @include('partials.candidates.avatar_img')</div>
                  <label class="float-right cover-cam"
                    ><b-icon icon="camera"
                  /></label>
                </div>
              </div>
            </div>
          </section>
           <section>
            <div class="line-across mt-5"></div>
          </section>

          <section class="p-4">
            <div class="row align-items-center">

              <div class="text-left font-14 mb-4 col-sm-12 col-md-12 col-lg-4 col-12">








                <h3>
                    {{ $candidate->user->listname }}
				@if ($candidate->interviewed) <i class="fa fa-check text-success" title="Interviewed"></i>@endif
				@if ($candidate->duplicate) <i class="fa fa-clone text-warning" title="Duplicate"></i>@endif
                </h3>
                <label >@include('partials.staticdisplay.field', ['fieldprompt'=>'',
															'fieldvalue'=> $candidate->jobtitle_text ])</label>

                	@include('partials.contact_view_grouped2', ['contacts'=>$candidate->contactfields])
                {{-- <label>EE status: White</label><br /> --}}


              </div>

              <div class="text-left font-14 mb-4 col-sm-12 col-md-12 col-lg-4 col-12">
                  <label>@if($candidate->idnumber) <span class="fa fa-id-card static-prompt" title="ID Number"></span> @endif
				{{ $candidate->idnumber }}
                       @include('partials.list_candidate_actions')</label><br>
              </div>
              <div class="text-left font-14 mb-4 col-sm-12 col-md-12 col-lg-4 col-12">
                  <div class="card candidate salary">
 {{-- <div class="card-header pt-1 pb-1">Salary</div> --}}
   <div class="card-body pt-1 pb-1">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Category',
															'fieldstatus'=> $candidate->salarycategory ])
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Expected Gross',
															'fieldvalue'=> $candidate->salary ])
                @include('partials.staticdisplay.field', ['fieldprompt'=>'System',
															'fieldvalue'=> optional($candidate->jobtitle)->description ])


						</div>
 </div>





            </div>

            {{-- <div class="text-left font-14 col-sm-12 col-md-12 col-lg-3 col-12">
                  <label>Female</label>
              </div> --}}

          </section>
          <section class="p-4">
            <div class="row align-items-center">

              <div  class="text-left font-14 mb-3 p-0 col-12">
                <div class="card-group">
                    @if($candidate->gender)
                        <div class="card candidate card-body pt-1 pb-1 border-0">
				            @include('partials.staticdisplay.field', ['fieldprompt'=>'Gender',
															'fieldstatus'=> optional($candidate->gender) ])
		                 </div>
                  @endif
                  <div class="card candidate card-body pt-1 pb-1 border-0">
				     @include('partials.staticdisplay.field', ['fieldprompt'=>'EE Status',
															'fieldstatus'=> $candidate->eestatus ])
		            </div>
                    <div class="card candidate card-body pt-1 pb-1 border-0">
				        @include('partials.staticdisplay.field', ['fieldprompt'=>'Upload Date',
															'fielddate'=> $candidate->activated_at ])
		            </div>
                    <div class="card candidate card-body pt-1 pb-1 border-0">
				        @include('partials.staticdisplay.field', ['fieldprompt'=>'Consultant',
															'fieldconsultant'=> $candidate->consultant ])
		            </div>
                    	<div class="card candidate card-body pt-1 pb-1 border-0 @if(($candidate->status_id == 2) && !($candidate->availability_id)) blinking_div @endif">
				            @include('partials.staticdisplay.field', ['fieldprompt'=>'Availability',
															'fieldstatus'=> $candidate->availability ])
		                </div>
		            <div class="card candidate card-body pt-1 pb-1 border-0">
				        @include('partials.staticdisplay.field', ['fieldprompt'=>'Status',
															'fieldstatus'=> $candidate->status ])
		            </div>





                </div>



                     </div>
            </div>
          </section>

           <section>
            <div class="line-across"></div>
          </section>
          <section class="p-4">
            <div class="row align-items-center">
              <div  class="text-left font-14 mb-3 p-0 col-12">

		@include('partials.collapse_textarea', ['field'=>'idealjob'.$candidate->id, 'field_title'=>'Ideal Job', 'start_expanded'=>'true', 'field_body'=>isset($candidate->idealjob) ? $candidate->idealjob->chunk : ''])
		@include('partials.collapse_textarea', ['field'=>'summary'.$candidate->id, 'field_title'=>'Summary', 'start_expanded'=>'true', 'field_body'=>isset($candidate->summary) ? $candidate->summary->chunk : ''])
		@include('partials.collapse_textarea', ['field'=>'agencynotes'.$candidate->id, 'field_title'=>'Agency Notes', 'field_min'=>true, 'start_expanded'=>'true', 'field_body'=>isset($candidate->agencynotes) ? $candidate->agencynotes->chunk : '',
		'edit_lt'=>true, 'edit_lt_route'=>route('notes.edit',['model'=>'candidates','modelid'=>$candidate->id]), 'edit_lt_target'=>'candidate'.$candidate->id
		])
		@include('partials.collapse_textarea', ['field'=>'skills'.$candidate->id, 'field_title'=>'Skills', 'start_expanded'=>'true', 'field_body'=>isset($candidate->sellme) ? $candidate->sellme->chunk : ''])




              </div>
            </div>
          </section>



</div>
{{--
<div class="card mb-3  candidate-outline" id={{ $candidate->id }}>
	<div class="card-header candidate">
		<div class="row">
		<div class="col-6">
			<div class="row">
				<div class="profile float-left mr-2">
					<div class="profile_pic_list">
					    @include('partials.candidates.avatar_img')
					</div>
				</div>
 <div class="float-left">
				@if (isset($jobad) && $jobad)
	    @if ($jobad->jobapplications->contains('candidate_id', $candidate->id))
			<i class="fa fa-check text-secondary" title="Selected"></i>
		@else
		<input type="checkbox" name="candIds[]" class="chk"  value="{{ $candidate->id }}" @if (is_array(session()->get('search_cand_'.$jobad->id)) && in_array($candidate->id, session()->get('search_cand_'.$jobad->id))) checked @endif>
		@endif
		@endif
			{{-- <span>	<h4>{{ $candidate->user->listname }}
				@if ($candidate->interviewed) <i class="fa fa-check text-success" title="Interviewed"></i>@endif
				@if ($candidate->duplicate) <i class="fa fa-clone text-warning" title="Duplicate"></i>@endif
        </h4> </span> --}}
 {{-- </div>
			</div> --}}
			{{-- <div class="row">
			<div class="col-6"> --}}
				{{-- @include('partials.list_candidate_actions') --}}
			{{-- </div> --}}
			{{-- <div class="col-6 "> --}}
			{{-- @if($candidate->idnumber) <span class="fa fa-id-card static-prompt" title="ID Number"></span> @endif
				{{ $candidate->idnumber }} --}}
			{{-- </div>
			</div>
			</div> --}}
			{{-- <div class="col-6"> --}}
				{{-- @include('partials.contact_view_grouped2', ['contacts'=>$candidate->contactfields]) --}}
			{{-- </div>
		</div> --}}
	{{-- </div>
	<div class="card-body  m-0 p-0 candidate">
		@if (isset($ranks) && !empty($ranks))
		<div class="row m-0 p-0">
		<div class="col-6 skillsrank">
			@if (isset($ranks['sellme']) && isset($ranks['sellme'][$candidate->id]))
				<h5>Skills Rank : {{ number_format($ranks['sellme'][$candidate->id], 2) }} </h5>
			@endif
		</div>
		<div class="col-6 cvrank">
			@if (isset($ranks['textcv']) && isset($ranks['textcv'][$candidate->id]))
				<h5>CV Rank : {{ number_format($ranks['textcv'][$candidate->id], 2) }} </h5>
			@endif
			</div>
		</div>
@endif

	<div class="card-group ">
		@if($candidate->gender) --}}
		{{-- <div class="card candidate card-body pt-1 pb-1 border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Gender',
															'fieldstatus'=> optional($candidate->gender) ])
		</div> --}}
		{{-- @endif --}}
		{{-- <div class="card candidate card-body pt-1 pb-1 border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'EE Status',
															'fieldstatus'=> $candidate->eestatus ])
		</div> --}}
		{{-- <div class="card candidate card-body pt-1 pb-1 border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Upload Date',
															'fielddate'=> $candidate->activated_at ])
		</div> --}}
		{{-- <div class="card candidate card-body pt-1 pb-1 border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Consultant',
															'fieldconsultant'=> $candidate->consultant ])
		</div> --}}
		{{-- <div class="card candidate card-body pt-1 pb-1 border-0 @if(($candidate->status_id == 2) && !($candidate->availability_id)) blinking_div @endif">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Availability',
															'fieldstatus'=> $candidate->availability ])
		</div>
		<div class="card candidate card-body pt-1 pb-1 border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Status',
															'fieldstatus'=> $candidate->status ])
		</div> --}}


		{{-- </div> --}}

		{{-- <div class="card-group"> --}}

{{-- <div class="card candidate location">
<div class="card-header pt-1 pb-1">Location</div>
<div class="card-body pt-1 pb-1">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Current',
															'fieldvalue'=> isset($candidate->location) ? optional($candidate->location)->description : '-' ])
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Preferred',
															'fieldvalue'=> optional($candidate->preferredlocations)->implode('description', ', ') ])

</div>
		</div> --}}
		{{-- <div class="card candidate jobtitle">

<div class="card-header pt-1 pb-1">Job Title</div>
<div class="card-body pt-1 pb-1">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'System',
															'fieldvalue'=> optional($candidate->jobtitle)->description ]) --}}

				{{-- @include('partials.staticdisplay.field', ['fieldprompt'=>'Actual',
															'fieldvalue'=> $candidate->jobtitle_text ]) --}}
{{-- </div>
</div> --}}
{{--
<div class="card candidate salary">
 <div class="card-header pt-1 pb-1">Salary</div>
   <div class="card-body pt-1 pb-1">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Category',
															'fieldstatus'=> $candidate->salarycategory ])
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Expected Gross',
															'fieldvalue'=> $candidate->salary ])
						</div>
 </div>
 </div> --}}


{{--
		@include('partials.collapse_textarea', ['field'=>'idealjob'.$candidate->id, 'field_title'=>'Ideal Job', 'start_expanded'=>'true', 'field_body'=>isset($candidate->idealjob) ? $candidate->idealjob->chunk : ''])
		@include('partials.collapse_textarea', ['field'=>'summary'.$candidate->id, 'field_title'=>'Summary', 'start_expanded'=>'true', 'field_body'=>isset($candidate->summary) ? $candidate->summary->chunk : ''])
		@include('partials.collapse_textarea', ['field'=>'agencynotes'.$candidate->id, 'field_title'=>'Agency Notes', 'field_min'=>true, 'start_expanded'=>'true', 'field_body'=>isset($candidate->agencynotes) ? $candidate->agencynotes->chunk : '',
		'edit_lt'=>true, 'edit_lt_route'=>route('notes.edit',['model'=>'candidates','modelid'=>$candidate->id]), 'edit_lt_target'=>'candidate'.$candidate->id
		])
		@include('partials.collapse_textarea', ['field'=>'skills'.$candidate->id, 'field_title'=>'Skills', 'start_expanded'=>'true', 'field_body'=>isset($candidate->sellme) ? $candidate->sellme->chunk : '']) --}}
	{{-- </div>
</div>  --}}

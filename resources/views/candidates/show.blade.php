@extends('layouts.tab')

@section('title', __($candidate->user->listname))

@section('tabheader')

    <div class="mt-5 candidate-container mb-5 p-0 container ">
		<div class="row">
			<div class="col-xs-9 col-sm-9 col-md-9">

				<div class="row">
				{{-- <div class="cprofile float-left mr-2">
					<div class="profile_pic cover-profile-pic">
						@include('partials.candidates.avatar_img')
					</div> --}}
				</div>
				{{-- <div class="float-left">
					<h3>{{ __($candidate->user->listname) }}
					@if ($candidate->interviewed) <i class="fa fa-check text-success" title="Interviewed"></i>@endif
				@if ($candidate->duplicate) <i class="fa fa-clone text-warning" title="Duplicate"></i>@endif
					</h3> --}}
				{{-- <div class="d-flex position-absolute absolute-bottom w-100 ">
					<button type="button"  class="profile_btn" data-toggle="collapse" data-target="#avatarDiv" aria-expanded="false" aria-controls="avatarDiv"  title="Edit Profile Pic">
						<i class="fa fa-portrait text-info"></i>
					</button>
				</div> --}}
				{{-- </div> --}}
				</div>
			</div>
			<div class="col-xs-3 col-sm-3 col-md-3 text-right">
				<h5>
				@if(($candidate->status_id == 2) && !($candidate->availability_id))<span class='blinking_div p-1'>Availability</span> @endif
						@include('partials.show_status', ['status'=>$candidate->availability, 'show_shadow'=>true])
						@include('partials.show_status', ['status'=>$candidate->status, 'show_shadow'=>true])
				</h5>
				<h5 class="scrollhide">
					<span class="badge" >{{ optional($candidate->consultant)->fullname_username }}</span>
				</h5>
				<h5 class="scrollhide">
					<span class="badge" >{{ $candidate->activated_at }}</span>
				</h5>
				<button type="button"  class="profile_btn" data-toggle="collapse" data-target="#avatarDiv" aria-expanded="false" aria-controls="avatarDiv"  title="Edit Profile Pic">
						<i class="fa fa-portrait text-info"></i>
					</button>
			</div>
		</div>

		<div class="collapse" id="avatarDiv">
			<form method="post" action="{{url('candidates/'.$candidate->id.'/avatarupload')}}" enctype="multipart/form-data"
						id="avatarForm" class="dropzone">
				<div class="row">
					<div class="col-xs-1 col-sm-1 col-md-1 float-right">
						<button type="submit" id="deleteavatar" title="Delete Profile Pic"><i class="fa fa-user-slash text-danger"></i></button>
					</div>
					<div class="col-xs-11 col-sm-11 col-md-11">
						<div class="dz-message" style="height:30px">Drop photo here or click to upload</div>
					</div>

				</div>
						@csrf
			</form>
		</div>

		<div class="table table-striped " id="avatar-preview" style="display:none">
			<div id="avatar-preview-template" class="" style="display:none">
				<div class="row col-md-12">
        <!-- This is used as the file preview template -->
					<div class="row col-md-4">
						<p class="name" data-dz-name></p>
						<strong class="error text-danger" data-dz-errormessage></strong>
					</div>
					<div class="row col-md-4">
						<p class="size" data-dz-size></p>
					</div>
					<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
						<div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
					</div>
				</div>

			</div>

		</div>
 {{--
		<div class="mt-5 candidate-container mb-5 p-0 container">
		 <div class="row justify-content-center">
		 <div class="col-md-12">

			<div class="card-group">
			<div class="card">
			 <div class="card-header addressheader">
            @include('partials.candidates.personaldetails_view')
            {{-- @include('partials.candidates.rating_view_block_card') --}}
			{{-- </div>
			</div>
			<div class="card">
			 <div class="card-header addressheader">

             @include('partials.candidates.rating_view_block_card')
			</div>

			</div>
			</div>
			</div>
		 </div>
		</div>

	</div>  --}}



@endsection

@section('content')



	<div class="card mt-5 candidate-container mb-5 p-0 container">
        <section class="p-4">
		<div class="card-body">

		@if (request()->skillsrank ||  request()->cvrank)
						<div class="row m-0 p-0">
							<div class="col-6">
			@if (request()->skillsrank)
								<h5>Skills Rank : {{ number_format(request()->skillsrank, 2) }} </h5>
			@endif
							</div>
							<div class="col-6">
			@if (request()->cvrank)
								<h5>CV Rank : {{ number_format(request()->cvrank, 2) }} </h5>
			@endif
							</div>
						</div>
@endif

		@if (!$duplicates->isEmpty())
			@include('partials.candidates.duplicates_list')
		@endif
	@include('staticwork.emailssentnotice', ['viewobj'=>'candidate'])
                    @csrf

						{{-- <div class="card-group">
@include('partials.candidates.personaldetails_view')
@include('partials.candidates.rating_view_block_card')
						</div> --}}


						</div>
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

                        <section><div class="line-across mt-5"></div></section>
											<div class="card-group">

					<h3>{{ __($candidate->user->listname) }}
					@if ($candidate->interviewed) <i class="fa fa-check text-success" title="Interviewed"></i>@endif
				 @if ($candidate->duplicate) <i class="fa fa-clone text-warning" title="Duplicate"></i>@endif
					</h3>

				</div>

						<div class="card-group nameheader">


	@include('partials.contacts.contact_view_block_card')
	@include('partials.candidates.jobtitle_view_block_card')
	@include('partials.candidates.location_view_block_card')
	@include('partials.salary_view_block_card', ['obj'=>$candidate, 'is_candidate'=>true])
	 {{-- @include('partials.addresses.address_view_block_card')  --}}


						</div>


						<div class="card-group">
{{-- @include('partials.candidates.location_view_block_card') --}}


						</div>


					@include('partials.collapse_textarea', ['field'=>'sellme', 'start_expanded'=>'true',
															'field_title'=>'Core Skills', 'field_body'=>optional($candidate->sellme)->chunk])
					@include('partials.collapse_textarea', ['field'=>'textcv', 'start_expanded'=>'false',
															'field_title'=>'Text CV', 'field_body'=>optional($candidate->textcv)->chunk])
					@include('partials.collapse_textarea', ['field'=>'interviewnotes', 'start_expanded'=>'true', 'field_min'=>true,
															'field_title'=>'Interview Notes', 'field_body'=>optional($candidate->interviewnotes)->chunk])
					@include('partials.collapse_textarea', ['field'=>'agencynotes', 'start_expanded'=>'true', 'field_min'=>true,
															'field_title'=>'Agency Notes', 'field_body'=>optional($candidate->agencynotes)->chunk,
															'edit_lt'=>true,
															'edit_lt_route'=>route('notes.edit',['model'=>'candidates','modelid'=>$candidate->id]),
															'edit_lt_target'=>'candidate'.$candidate->id])
					@include('partials.collapse_textarea', ['field'=>'idealjob', 'start_expanded'=>'true',
															'field_title'=>'Ideal Job', 'field_body'=>optional($candidate->idealjob)->chunk])
					@include('partials.collapse_textarea', ['field'=>'summary', 'start_expanded'=>'false',
															'field_title'=>'Summary', 'field_body'=>optional($candidate->summary)->chunk])

@section('formbuttons')
								<a href="{{ route('candidates.edit',$candidate->id) }}" data-toggle="tooltip" title="Edit" class="btn btn-primary" target="candidate{{ $candidate->id }}"><i class="fa fa-edit"></i></a>
								<a class="btn btn-danger" title="Match Job" target="matchjob{{ $candidate->id }}"  href="{{ route('jobs.index').'?candid='.$candidate->id }}">
							@include('partials.icons.search_job')
								</a>

					<a class="btn btn-danger" title="Link To Client" target="linkclient{{ $candidate->id }}"  href="{{ route('clients.index').'?candid='.$candidate->id }}" target="_blank">
							@include('partials.icons.link_client')
							</a>
								<button type="button" class="btn btn-close">{{ __('Close') }}</button>
@endsection
@include('partials.footer.formbuttonsSection')
@yield('contentbuttons')


			@include('partials.documents_card', ['model'=>'candidates', 'modelid'=>$candidate->id])



						<div class="card job-outline">
							<div class="card-header coloration candidate1 job2 text-light">
								<h4>Jobs Candidate Sent To</h4>
							</div>
							<div class="card-body">
				@include('partials.candidates.jobapplication_summary_list', ['jobapplications'=>$candidate->jobapplicationsSentTo])
							</div>
						</div>

						<div class="card client-outline">
							<div class="card-header coloration candidate1 client2 text-light">
								<h4>Clients Expressing Interest</h4>
							</div>
							<div class="card-body">
				@include('partials.candidates.directapplication_summary_list', ['jobapplications'=>$candidate->directapplications])
							</div>
						</div>

@include('partials.email_trail')

						<div class="card job-outline">
							<div class="card-header job">
								<h4>Jobs Shortlisted</h4>
							</div>
							<div class="card-body">
				@include('partials.candidates.shortlist_summary_list', ['jobapplications'=>$candidate->jobapplicationsShortlisted])
							</div>
						</div>

@include('partials.audit_trail', ['statusArr'=>$statuses])



		</div>


@endsection

@push('scripts')
<script src="{{ asset('js/jquery.collapser.min.js') }}" rel="javascript"></script>
@include('scripts.src_datatables')
@endpush

@section('js')
@parent

 					@include('partials.dropzone_def_js', ['modelurl'=>'candidates/'.$candidate->id])

<style>
.avatarForm {
	min-height:20px;
	height : 50px;
	width : 100px;
	padding: 0;
}
.avatarForm .dz-message{
	text-align:top;
			margin:0;
			}
			</style>

<script>
//	Dropzone.options.avatarForm = false;
    Dropzone.options.avatarForm = {
		uploadMultiple: false,
        paramName: 'file',
        maxFilesize: 1,
			previewsContainer: document.getElementById("avatar-preview"),
			previewTemplate: document.getElementById("avatar-preview-template").innerHTML,
		createImageThumbnails: false,
        acceptedFiles: '.jpg, .jpeg, .png',
        maxFiles: 10,
        init: function()
        {
            this.on("success", function(file, response)
            {
                var path = "/avatars/" + response;
                $("#user-avatar").attr("src", path );
            });
        }
    };

		$("document").ready(function() {

				$('.p1').collapser({
		mode: 'lines',
		truncate: 20
	});
	 $('.filterable').DataTable( {
			"lengthMenu" : [[10, -1],[10, "All"]]
	 } );
	 $('.filterable').each(function(i, obj) {
		 if ($(obj).attr('data-page-length')) {
	 $('#DataTables_Table_'+i+'_length select').prepend( '<option value="'+$(obj).attr('data-page-length')+'">Active</option>' );
		 }
    //test
});

		});

</script>
@stop

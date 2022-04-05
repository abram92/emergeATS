@extends('layouts.menu')

@section('contentsearch')
 
    <div class="searchForm">
			@csrf
 
@include('partials.searchfilters.navbar_search_filter', ['filterName'=> 'q_title',
														'filterVar'=> ((isset($q_title)) ? $q_title : null),
														'filterPlaceholder'=> 'Filter Event Title',
														'advancedFilter'=>true, 
														'filterPrefix'=>'evFilter', 
														'isExpanded'=>false,
														'canFilter'=>true,
														'canSearch'=> false, 
														'canExport'=> false])							


	<div id="evFilterBody" class="searchFilters searchForm collapse " aria-labelledby="evFilterHeading">
		<div class="container-fluid p-0" style="max-height:80vh; overflow-y:auto;">
			<div class="card card-body w-100">
				<div class="row">
					<div class="col-md-12">
@include('partials.select2_filter_dropdown_multiple', ['fieldid'=>'q[eventtypes][]', 'fieldname'=>'eventtypes', 'fieldlabel'=>'Event Types', 
									'fieldplaceholder'=>'Choose Event Types', 
									'options'=>$alleventtypes,
									'selectedoptions'=>old('q[event_types]', isset($q['event_types']) ? $q['event_types'] : null)])
					</div>
				</div>
@if (!empty($allconsultants))	
				<div class="row">	
					<div class="col-md-12">
@include('partials.select2_filter_dropdown_multiple', ['fieldid'=>'q[consultants][]', 'fieldname'=>'consultants', 'fieldlabel'=>'Consultants', 
									'fieldplaceholder'=>'Select Consultants', 
									'options'=>$allconsultants,
									'selectedoptions'=>old('q[consultants]', isset($q['consultants']) ? $q['consultants'] : null)])									
					</div>					
				</div>								
@endif			
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'candidatelist', 
									'fieldid'=>'q[candidatelist][]', 
									'fieldlabel'=>'Candidates', 
									'select2_class'=>'select-select2-optional select2-fetch',
									'fieldplaceholder'=>'Select Candidates', 
									'options'=>null,
									'selectedoptions'=>old('candidatelist', isset($candidatelist) ? $candidatelist : null)])

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'joblist', 
									'fieldid'=>'q[joblist][]', 
									'fieldlabel'=>'Job References', 
									'select2_class'=>'select-select2-optional select2-fetch',
									'fieldplaceholder'=>'Select Jobs', 
									'options'=>null,
									'selectedoptions'=>old('joblist', isset($joblist) ? $joblist : null)])

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'clientlist', 
									'fieldid'=>'q[clientlist][]', 
									'fieldlabel'=>'Clients', 
									'select2_class'=>'select-select2-optional select2-fetch',
									'fieldplaceholder'=>'Select Clients', 
									'options'=>null,
									'selectedoptions'=>old('clientlist', isset($clientlist) ? $clientlist : null)])				
			</div>		
		</div>
		@include('partials.form_reset')	
	</div>
	</div>
	
	<div class="topbar-divider d-none d-sm-block">
	</div>

	<div class="topadd">
		<div class="dropdown btn btn-xs btn-navbar" id='external-events-list'>
			<a href="#" class="text-secondary" data-toggle="" title="Drag New Event to Calendar" id="dragEvents">@include('partials.icons.add_event')</a>
			<div class='dropdown-content' aria-labelledby="dragEvents">
	  @foreach($alleventtypes as $eventtype)
        <div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
			<div class='fc-event-main'  data-id="{{ $eventtype['id'] }}" @if($eventtype['colour_hex']) style="background-color:{{ $eventtype['colour_hex'] }}" @endif>
				{{ $eventtype['description'] }}
			</div>
        </div>
		@endforeach
			</div>
		</div>
	</div>
	
<div class="topbar-divider d-none d-sm-block"></div>
	  
@stop		


@section('content')

<link href='js/fullcalendar/lib/main.css' rel='stylesheet' />
<script src='js/moment.min.js'></script>
<script src='js/fullcalendar/lib/main.js'></script>

<style>
.fc-more-popover {
	max-height: 80%;
    overflow-y: scroll;
}
</style>
 
<div id="selectDateModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4>Select Date</h4>
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
			</div>
			<div id="modalBody" class="modal-body">
	@include('partials.form_date', ['fieldname'=>'selectdate', 'fieldlabel'=>'Select Date'])
			</div>
			<div class="modal-footer">
				<div class="form-group row">
					<button type="submit" id="selectdatebtn" class="btn btn-success">Select</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>		
			</div>	
		</div>
	</div>
</div>

<div id="viewModal" class="modal fade">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header evnt container-fluid col-12">
<h4 id="modalTitle" class="modal-title"><span name="title" id="title"></span></h4>
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
		</div>
        <div id="modalBody" class="modal-body">
		<div class="p-0 mb-2 row col-12"><span class="badge1 mr-auto ml-1" id="eventuser"></span><span class="badge1 ml-auto mr-1" id="eventtype"></span></div>
		<div id="message"></div>		
		<div id="modalForm" class="container-fluid">


			<div class="form-group row">
			<div class="col-6">
		    @include('partials.staticdisplay.field', ['fieldprompt'=>'Start',
			                                          'fieldid'=>'start_date',
															'fieldspan'=> '' ])
															</div>
			<div class="col-6">
		    @include('partials.staticdisplay.field', ['fieldprompt'=>'End',
			                                          'fieldid'=>'end_date',
															'fieldspan'=> '' ])
															</div>															
            </div>
			<div class="form-group row">
			<div class="col-12">
		    @include('partials.staticdisplay.field', ['fieldprompt'=>'Comments',
			                                          'fieldid'=>'comments',
															'fieldspan'=> '' ])
			</div>
			</div>															

			<div class="form-group row">
			<div class="col-12">
		    @include('partials.staticdisplay.field', ['fieldprompt'=>'Candidates',
													  'promptclass'=>'candidate', 
			                                          'fieldid'=>'candidatelist',
															'fieldspan'=> '' ])
			</div>
			</div>															
			
			<div class="form-group row">
			<div class="col-12">
		    @include('partials.staticdisplay.field', ['fieldprompt'=>'Job References',
													  'promptclass'=>'job', 
			                                          'fieldid'=>'joblist',
															'fieldspan'=> '' ])
			</div>
			</div>															

			<div class="form-group row">
			<div class="col-12">
		    @include('partials.staticdisplay.field', ['fieldprompt'=>'Clients',
													  'promptclass'=>'client', 
			                                          'fieldid'=>'clientlist',
															'fieldspan'=> '' ])
			</div>
			</div>															
	
			
		</div>
		</div>
        <div class="modal-footer">
                        <div class="form-group row">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>		
        </div>
    </div>
</div>
</div> 

<div id="editModal" class="modal fade">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header evnt">
            <h4 id="modalTitle" class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
        </div>
        <div id="modalBody" class="modal-body">
		<div id="message"></div>
		<form id="modalForm">
			<input class="form-control" type="hidden" id="event_id">
			
<div class="col-xs-12 col-sm-12 col-md-12">
@include('partials.form_text', ['fieldname'=>'title', 
								'fieldlabel'=>'Title', 
								'is_autofocus'=>true,
								'fielddefault'=> old('title', isset($event) ? $event->title : null ),
								'is_required'=>true])		
	@include('partials.select2_dropdown_single', ['fieldname'=>'type_id', 
									'fieldid'=>'type_id',
									'fieldlabel'=>'Type', 
									'required'=>true,
									'fieldplaceholder'=>'Choose Event Type', 
									'options'=>$alleventtypes,
									'selectedid'=>old('type_id', isset($event) ? $event->type_id : null )])	
</div>			

			<div class="form-label-group in-border row">
				<div class="input-group-append col-md-12">
					<input class="form-control" required name="start_date" type="date" id="start_date">
					<input class="form-control" name="start_time" type="time" id="start_time">
					<label for="start_date" class="col-form-label text-md-right">Start</label>					
				</div>	
            </div>	
			<div class="form-label-group in-border row">
				<div class="input-group-append col-md-12">
					<input class="form-control" name="end_date" type="date" id="end_date">
					<input class="form-control" name="end_time" type="time" id="end_time">
					<label for="end_date" class="col-form-label text-md-right">End</label>					
                </div>
            </div>	
		
<div class=" col-xs-12 col-sm-12 col-md-12">		
			<div class="form-label-group in-border row">
					<textarea placeholder="Comments" class="form-control" name="comments" id="comments"></textarea>
				<label for="comments" class="col-form-label text-md-right">Comments</label>
            </div>	
</div>

@include('partials.select2_dropdown_multiple', ['fieldname'=>'candidatelist', 
									'fieldid'=>'candidatelist', 
									'fieldlabel'=>'Candidates', 
									'select2_class'=>'select-select2-optional select2-fetch',
									'fieldplaceholder'=>'Select Candidates', 
									'options'=>null,
									'selectedoptions'=>old('candidatelist', isset($candidatelist) ? $candidatelist : null)])

@include('partials.select2_dropdown_multiple', ['fieldname'=>'joblist', 
									'fieldid'=>'joblist', 
									'fieldlabel'=>'Job References', 
									'select2_class'=>'select-select2-optional select2-fetch',
									'fieldplaceholder'=>'Select Jobs', 
									'options'=>null,
									'selectedoptions'=>old('joblist', isset($joblist) ? $joblist : null)])

@include('partials.select2_dropdown_multiple', ['fieldname'=>'clientlist', 
									'fieldid'=>'clientlist', 
									'fieldlabel'=>'Clients', 
									'select2_class'=>'select-select2-optional select2-fetch',
									'fieldplaceholder'=>'Select Clients', 
									'options'=>null,
									'selectedoptions'=>old('clientlist', isset($clientlist) ? $clientlist : null)])

			
		</form>
		</div>
        <div class="modal-footer">
                        <div class="form-group row">
								<button type="submit" class="btn btn-success">Save</button>
								<button type="submit" class="btn btn-danger">Delete</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>		
        </div>
    </div>
</div>
</div>

<div class="container-fluid card p-4">
@include('partials.flashmessages')

	<div id='filtereventtypes'></div>  
	<div id='filterconsultants'></div>  
	<div id='filtercandidates'></div>  
	<div id='filterjobs'></div>  
	<div id='filterclients'></div>  
	<div class="response"></div>
	<div id='calendar'></div>  
</div>

@endsection 
@push('scripts')
@include('scripts.src_select2')
@endpush

@section('js')
@parent
<script>

var calendar;

document.addEventListener('DOMContentLoaded', function() {

    /* initialize the external events
    -----------------------------------------------------------------*/

	var containerEl = document.getElementById('external-events-list');
    new FullCalendar.Draggable(containerEl, {
      itemSelector: '.fc-event',
      eventData: function(eventEl) {
        return {
          title: eventEl.innerText.trim()
        }
      }
    });

        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });


    /* initialize the calendar
    -----------------------------------------------------------------*/

    var calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
		editable: true,	
@if (Session::has('calInitialDate'))
		initialDate: "{{ Session::get('calInitialDate') }}",
{{ Session::forget('calInitialDate') }}
	
@endif		
		customButtons: {
			selectDate: {
				text: 'Select Date',
				click: function() {
					$("#selectDateModal").modal();
//		$("#calendar").fullCalendar('gotoDate', date);
				}
			}
		},	  
		headerToolbar: {
			left: 'prevYear,prev,next,nextYear today selectDate',
			center: 'title',
			right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth,listWeek,listDay'
		},
		views: {
			listMonth: { buttonText: 'list month' },
			listDay: { buttonText: 'list day' },
			listWeek: { buttonText: 'list week' }
		},
		dayMaxEvents: true,
		navLinks: true,
		events: {
			url : "{{ route('eventfeed') }}",
			extraParams : function() {
				r = [];
				consultantids = $('[name="consultants"]').val();
				if (consultantids.length)
					r['consultant_ids'] = consultantids;
				eventtypeids = $('[name="eventtypes"]').val(); //document.getElementById('eventtype_ids').selected
				if (eventtypeids.length)
					r['event_type_ids'] = eventtypeids;
				candids = $('#evFilterBody [name="candidatelist"]').val();
				if (candids.length)
					r['cand_ids'] = candids;
				jobids = $('#evFilterBody [name="joblist"]').val();
				if (jobids.length)
					r['job_ids'] = jobids;
				clientids = $('#evFilterBody [name="clientlist"]').val();
				if (clientids.length)
					r['client_ids'] = clientids;
				titleval = $('[name="q_title"]').val(); //document.getElementById('eventtype_ids').selected
				if (titleval.length)
					r['title'] = titleval;
                return r;				
/*				return {
					consultant_ids: $('[name="consultants"]').val(), //document.getElementById('consultant_ids').selected,
					event_type_ids: $('[name="eventtypes"]').val(), //document.getElementById('eventtype_ids').selected
					cand_ids: $('#evFilterBody [name="candidatelist"]').val(),
					job_ids: $('#evFilterBody [name="joblist"]').val(),
					client_ids: $('#evFilterBody [name="clientlist"]').val(),
					title: $('[name="q_title"]').val() //document.getElementById('eventtype_ids').selected
				} */
			},
			failure: function() {
				displayMessage('Error fetching events', 'danger');
			},
			success: function(data) {
				var fet = $('#filtereventtypes').empty(); 
				$('[name="eventtypes"] option:selected').each(function(index,item) {
					var filtertypes = document.createElement("span");
					filtertypes.innerText = item.text; 	
					var typebullet = document.createElement("span");
					filtertypes.classList.add("mr-3");
					typebullet.classList.add("badge1", "mr-1");
					typebullet.style.background = item.style.backgroundColor;
					typebullet.style.border = 'solid 1px';
					typebullet.style.borderColor = 'black';
					filtertypes.prepend(typebullet);
//                    eventuser.classList.add("badge1");
//					eventuser.style.background = item.style.backgroundColor;
//					eventuser.style.color = item.style.color;
					
					fet.append(filtertypes);
				});
				if (fet.children().length) {
					var ftheader = document.createElement("span");
					ftheader.innerText = 'Event Types:';
					ftheader.style.fontWeight = 'bold';
					ftheader.classList.add("mr-1");
					fet.prepend(ftheader);
				}
                var fc = $('#filterconsultants').empty(); 
				$('[name="consultants"] option:selected').each(function(index,item) {
					var filteruser = document.createElement("span");
                    filteruser.innerText = item.text; 				
                    filteruser.classList.add("badge1","mr-1", "mb-1", "usercol"+(index % 10));
					fc.append(filteruser);
				});
				if (fc.children().length) {
					var ftheader = document.createElement("span");
					ftheader.innerText = 'Consultants:';
					ftheader.style.fontWeight = 'bold';
					ftheader.classList.add("mr-1");
					fc.prepend(ftheader);
				}
                var fc = $('#filtercandidates').empty(); 
				$('#evFilterBody [name="candidatelist"] option:selected').each(function(i, item){				
					var filtercand = document.createElement("span");
                    filtercand.innerText = item.text; 				
                    filtercand.classList.add("mr-3", "mb-1");
					fc.append(filtercand);
				});
				if (fc.children().length) {
					var ftheader = document.createElement("span");
					ftheader.innerText = 'Candidates:';
					ftheader.style.fontWeight = 'bold';
					ftheader.classList.add("mr-1");
					fc.prepend(ftheader);
				}		
                fc = $('#filterjobs').empty(); 
				$('#evFilterBody [name="joblist"] option:selected').each(function(i, item){		
					var filtercand = document.createElement("span");
                    filtercand.innerText = item.text; 				
                    filtercand.classList.add("mr-3", "mb-1");
					fc.append(filtercand);
				});
				if (fc.children().length) {
					var ftheader = document.createElement("span");
					ftheader.innerText = 'Jobs:';
					ftheader.style.fontWeight = 'bold';
					ftheader.classList.add("mr-1");
					fc.prepend(ftheader);
				}
                fc = $('#filterclients').empty(); 
				$('#evFilterBody [name="clientlist"] option:selected').each(function(i, item){		
					var filtercand = document.createElement("span");
                    filtercand.innerText = item.text; 				
                    filtercand.classList.add("mr-3", "mb-1");
					fc.append(filtercand);
				});
				if (fc.children().length) {
					var ftheader = document.createElement("span");
					ftheader.innerText = 'Clients:';
					ftheader.style.fontWeight = 'bold';
					ftheader.classList.add("mr-1");
					fc.prepend(ftheader);
				}				
			}
		},
@if (isset($publicholidays) && !empty($publicholidays)) 
		dayRender: function(dayRenderInfo) {
			if ($.inArray(dayRenderInfo.date, [{{ $publicholidays }}]) >= 0)
				dayRenderInfo.addClass('publicholiday');
		},
@endif
		eventDidMount: function(info) {
			if ( $(info.el).find(".badge-pill") ) {
				var eventuser = document.createElement("span");
                var usertag = info.event._def.extendedProps['usertag'];
                if (!(usertag)) {
                    // logged in user
                } else {
					eventuser.innerText = usertag; 				
					var usercol = info.event._def.extendedProps['usercol'];
                    eventuser.classList.add("badge","usercol"+usercol);

                    var fcevent = info.el.querySelector('.fc-event-time'); // for gridView
                    if (fcevent ) { 
                       fcevent.textContent += " ";
                       fcevent.append(eventuser);
                    }
                    fcevent = info.el.querySelector('.fc-list-event-graphic');  // for listView
                    if (fcevent ) { 
                       fcevent.textContent += " ";
                       fcevent.append(eventuser);
                    }
                }
            } else {
            }    
        },		
		eventContent (args) {

		},		
		eventChange: function (info) {
		},		
		droppable: true, // this allows things to be dropped onto the calendar
		eventReceive: function(info) {
			elem = info.draggedEl.innerHTML;
			var event_type = $(elem).data("id");
//	var title = $(elem).text();
//     $.extend(info.event, {type_id: event_type});
			info.event.setExtendedProp( 'type_id', event_type );

			modifyEvent(info.event, function(d) {
				event_id = d;
			});

//		console.log(info.event);
			if (event_id) {
//			$.extend(info.event, {id: event_id});			
				info.event.setProp('id', event_id);
				info.event.setProp('editable', true);
//			info.event.setExtendedProp( 'type_id', event_type );
//			info.event.remove;
			}
//			console.log(info.event);
		},
		selectable: true,
		select: 	function(event) {

//			$('#modalForm')[0].reset();
			var start_date = (event.start == null) ? event.start : moment(event.start).format('YYYY-MM-DD');
			var start_time = (event.start == null) ? event.start : moment(event.start).format('HH:mm');
		    var end_date = (event.end == null) ? event.end : moment(event.end).format('YYYY-MM-DD');
			var end_time = (event.end == null) ? event.end : moment(event.end).format('HH:mm');
			$('#message').html('');
            $('#editModal #modalTitle').text('Add Event');
			$('#editModal button.btn-danger').css('display', 'none');			
			$('#editModal #title').val(event.title);
			$('#editModal #type_id').val('');
			$('#editModal #type_id').trigger('change.select2');
			$('#editModal #event_id').val(event.id);
			$('#editModal #start_date').val(start_date);
			$('#editModal #start_time').val(start_time);
			$('#editModal #end_date').val(end_date);
			$('#editModal #end_time').val(end_time);			
            $('#editModal #modalBody').html(event.description);
            $('#editModal #eventUrl').attr('href',event.url);
			setSelectList('#candidatelist', []);
			setSelectList('#clientlist', []);
			setSelectList('#joblist', []);			
            $('#editModal').modal();
			
 //       calendar.unselect()
		},
		eventMouseLeave: function(info) {
			if (info.event.startEditable) {
				var e = info.el.querySelector(".evbtns");
				e.parentElement.removeChild(e);
			}
        }, 		
		eventMouseEnter: function(info) {
			if (info.event.startEditable) {
				
				var titleEl;
				var buttonsEl;
				var islist = info.view.type.includes('list');
				if (islist) {
					titleEl = info.el.querySelector(".fc-list-event-title");
					buttonsEl = document.createElement("span");					  
				} else {
					titleEl = info.el.querySelector(".fc-event-title");
					buttonsEl = document.createElement("span");	
//                    buttonsEl.style.zIndex = "99";					
				}
				if (titleEl) { 
					buttonsEl.classList.add("evbtns", "dropdown");
					var eventedit = document.createElement("button");
					eventedit.setAttribute("title","Edit");
					eventedit.classList.add("editbtn", "btn", "btn-sm","fa", "fa-edit", "text-primary");
				
					var eventdel = document.createElement("button");
					eventdel.setAttribute("title","Delete");
					eventdel.classList.add("delbtn", "btn", "btn-sm","fa", "fa-trash", "text-danger");
					buttonsEl.append(eventedit);
					buttonsEl.append(eventdel);
					if (islist) {
						buttonsEl.classList.add("bg-light");
						titleEl.append(buttonsEl);					
					} else {
						buttonsEl.classList.add("bg-dark", "mr-1");
						titleEl.prepend(buttonsEl);					
					}
				}
			}			
		},  
		eventDrop:  function(info) {
			if(!confirm("Are you sure about this change?")) {
				info.revert();
			} else
				modifyEvent(info.event, function(d){});
		},
		eventResize:  function(info) {
			if(!confirm("Are you sure about this change?")) {
				info.revert();
			} else
				modifyEvent(info.event, function(d){});			
		},	  
		eventClick:  function(info) {
			
			isDelete = $(info.jsEvent.target).hasClass("delbtn");
			
			if (isDelete) {
				if (confirm("Do you really want to delete?"))
					deleteEvent(info.event.id);
			} else {
				isEdit = $(info.jsEvent.target).hasClass("editbtn");

				var start_date = (info.event.start == null) ? info.event.start : moment(info.event.start).format('YYYY-MM-DD');
				var start_time = (info.event.start == null) ? info.event.start : moment(info.event.start).format('HH:mm');
				var end_date = (info.event.end == null) ? info.event.end : moment(info.event.end).format('YYYY-MM-DD');
				var end_time = (info.event.end == null) ? info.event.end : moment(info.event.end).format('HH:mm');
			
//			console.log(info.event.editable);
				var type_id = info.event.extendedProps.type_id;
				if (!type_id) {
					type_id = info.event.type_id;
				}
				var usertag = info.event._def.extendedProps['usertag'];

				if (isEdit && info.event.startEditable) {
//		if (usertag) {
					$('#message').html('');
					$('#editModal #modalTitle').text('Edit Event');
					$('#editModal button.btn-danger').css('display', 'flex');
					$('#editModal #event_id').val(info.event.id);
					$('#editModal #title').val(info.event.title);
					$('#editModal #type_id').val(type_id);
					$('#editModal #type_id').trigger('change.select2');
					$('#editModal #start_date').val(start_date);
					$('#editModal #start_time').val(start_time);
					$('#editModal #end_date').val(end_date);
					$('#editModal #end_time').val(end_time);	
					setSelectList('#candidatelist', info.event.extendedProps.candidates);
					setSelectList('#clientlist', info.event.extendedProps.clients);
					setSelectList('#joblist', info.event.extendedProps.jobs);

					$('#editModal #modalBody').html(info.event.description);
					$('#editModal #eventUrl').attr('href',info.event.url);
					$('#editModal').modal();
				} else {
					$('#message').html('');
					$('#viewModal button.btn-danger').css('display', 'flex');
					$('#viewModal #event_id').val(info.event.id);

					$('#viewModal #title').text(info.event.title);
					status = $('#viewModal #eventtype');
		   
					if (!(usertag)) {
                    // logged in user
					} else {
						var eventuser = document.createElement("span");
						eventuser.innerText = usertag; 				
						var usercol = info.event._def.extendedProps['usercol'];
						eventuser.classList.add("badge","usercol"+usercol);
						$('#viewModal #eventuser').empty();
						$('#viewModal #eventuser').append(eventuser);
					}	
										
					$('[name="eventtypes"] option').each(function(index,item) {
						if (info.event.extendedProps.type_id == item.value) {
							$('#viewModal #eventtype').text(item.text);
							status.text = item.text;
							$('#viewModal #eventtype').css('background-color', item.style.backgroundColor);
							$('#viewModal #eventtype').css('color', item.style.color);
						}
					});		   
					var start_text = (start_date) ? start_date+' '+start_time : '';
					var end_text = (end_date) ? end_date+' '+end_time : '';			
					$('#viewModal #start_date').text(start_text);
					$('#viewModal #end_date').text(end_text);
					listSelected('#candidatelist', info.event.extendedProps.candidates);
					listSelected('#clientlist', info.event.extendedProps.clients);
					listSelected('#joblist', info.event.extendedProps.jobs);

					$('#viewModal #modalBody').html(info.event.description);
					$('#viewModal #eventUrl').attr('href',info.event.url);
					$('#viewModal').modal();
				}
			}
        }
    });
    calendar.render();

	
	$("#selectdatebtn").click(function(){
		var dateString = $('#selectdate').val();
		if (moment(dateString, 'YYYY-MM-DD', true).isValid()) {
			$("#selectDateModal").modal('hide');
			calendar.gotoDate(dateString);
		} else {
			alert('Invalid date');
		}
	});
	  
	$('button[name="filter"]').click(function(){
		$("#evFilterBody").removeClass("show");
		calendar.refetchEvents();
	});	  
});


$("document").ready(function() {

@include('scripts.ready_select2')
	@include('scripts.ready_select2_form_reset');
		
	$('#editModal button.btn-success').click(function() {
			
		let id = $('#editModal #event_id').val();
		let title = $('#editModal #title').val();
		let event_type = $('#editModal #type_id').val();
		let start_d = $('#editModal #start_date').val()
		let start_t = $('#editModal #start_time').val();
		let end_d = $('#editModal #end_date').val()
		let end_t = $('#editModal #end_time').val();
		let comments = $('#editModal #comments').val();
		let candidates = getSelectedOptionsAsArray('candidatelist'); //$('#candidatelist').val();
		let clients = getSelectedOptionsAsArray('clientlist'); //$('#clientlist').val();
		let jobs = getSelectedOptionsAsArray('joblist'); //$('#joblist').val();
			
		let start = (start_d != '') ? start_d+((start_t != '')? 'T'+start_t: '') : null;
		let end = (end_d != '') ? end_d+((end_t != '')? 'T'+end_t: '') : null; // start_d+'T00:00';

		time_start = new Date(start.trim());

		time_end = (end && (end.trim() != 'T')) ? new Date(end.trim()) : null;

		if (id == ''){
			event = calendar.addEvent ({
              title: title,
              start: time_start,
			  editable:true,
			  allDay: false,
            });
		} else {
			event = calendar.getEventById(id);
			event.setProp('title', title);
			event.setAllDay( false );
			event.setStart(time_start);

		}
		
		if ((start_t == '')||(start_t == '00:00'))
			event.setAllDay( true );				
		event.setEnd(time_end);
		event.setExtendedProp( 'type_id', event_type );				
		event.setExtendedProp( 'comments', comments );		
		event.setExtendedProp( 'candidates', candidates );
		event.setExtendedProp( 'clients', clients );
		event.setExtendedProp( 'jobs', jobs );

		$('[name="eventtypes"] option').each(function(index,item) {
			if (event_type == item.value) {
				event.setProp('backgroundColor',  item.style.backgroundColor);
				event.setProp('textColor',  item.style.color);
			}
		});
// console.log(event);
		modifyEvent(event, function(event_id){
			if (id == ''){
				event.setProp('id', event_id);
			} else
				displayMessage('Event updated');

		});		
		$('#editModal').modal('hide');

	});



	$('#editModal button.btn-danger').click(function() {
			
		let id = $('#editModal #event_id').val();
		var deleteMsg = confirm("Do you really want to delete?");
		
		if (deleteMsg) {
		if (id == ''){
		} else {
			deleteEvent(id);
		}
		}
	});	
	  
@include('scripts.ready_select2_fetch_autocomplete')
  

});

	function getSelectedOptionsAsArray(id) {
		r = [];
		$('#'+id+' option').each(function(i, item){		
            r.push({ id: item.value, text: item.text});
        });
        return r;		
	}

	function displayMessage(message, divclass='success') {
		$(".response").html("<div class='alert alert-"+divclass+" alert-block'>"+message+"</div>");
		setInterval(function() { $(".alert-"+divclass).fadeOut(); }, 1000);
	}
	
	function displayErrors(response) {
		let boxAlert = `<div class="alert alert-danger">`;
		for (fields in response) {
			boxAlert += `<span>${response[fields]}</span><br/>`;
		}
		boxAlert += `</div>`;
		return boxAlert;
	}
	
	function listSelected(element, data) {
		var sp = $("#viewModal "+element);		
		sp.text('');
		$.each(data, function(idx, el) {
			sp.append('<div>'+el.text+'</div>');
		});					
	}
	
	function setSelectList(element, data) {
		var select = $("#editModal "+element);
		if(select.prop) {
			var options = select.prop('options');
		}
		else {
			var options = select.attr('options');
		}
		$('option', select).remove();			
		$.each(data, function(idx, el) {
			options[options.length] = new Option(el.text, el.id);
		});
			
		$("#editModal "+element+" > option").prop("selected", "selected");
        select.trigger("change");		
	}		
	
	
	function modifyEvent(event, callback) {

		var start = (event.start == null) ? event.start : moment(event.start).format('YYYY-MM-DD HH:mm');
		var end = (event.end == null) ? start : moment(event.end).format('YYYY-MM-DD HH:mm');
		var type_id = (event.type_id != null) ? event.type_id : event.extendedProps.type_id;

		var comments = (event.extendedProps.comments != null) ? event.extendedProps.comments : '';
		var candidates = (event.extendedProps.candidates != null) ? event.extendedProps.candidates.map(function (obj) {
								return obj.id;
						}) : [];
		var clients = (event.extendedProps.clients != null) ? event.extendedProps.clients.map(function (obj) {
								return obj.id;
						}) : [];
		var jobs = (event.extendedProps.jobs != null) ? event.extendedProps.jobs.map(function (obj) {
								return obj.id;
						}) : [];
				
		dt = start.split(' ');
		start_date = dt[0];
		start_time = (1 in dt) ? dt[1] : '';
		dt = end.split(' ');
		end_date = dt[0];
		end_time = (1 in dt) ? dt[1] : '';						

		let data = { 	_token: "{{ csrf_token() }}",
						"title":event.title,
						"type_id":type_id,
						"start_date":start_date,
						"start_time":start_time,
						"end_date":end_date,
						"end_time":end_time,
						"comments":comments,
						"candidatelist":candidates,
						"clientlist":clients,
						"joblist":jobs,
					};
		var event_id = event.id;
		if (!event_id){
			http_method = 'POST';
			url = 'calendarevents';
		} else {
			$.extend(data, { id: event_id } );
			http_method = 'PUT';
			url = "/calendarevents/"+event_id;
		}		
		$.ajax({
			type:http_method,
			url: url,
			data:data,
			success:function(msg){
				event_id = msg.insert_id;				
				callback(event_id);
			},
			error:function(msg){
				alert('Failed');
				callback(0);
			}
		});
	}		
		
	function deleteEvent(id) {
					http_method = 'DELETE';
			url = "/calendarevents/"+id;
	
			$.ajax({
				type: http_method,
				url: url,
				traditional:true,
				success:function(msg){
					if (msg) {
						$('#editModal').modal('hide');
						event = calendar.getEventById(id);
						event.remove();
//				console.log(msg);
					}
				},
				error:function(msg){
					$('#message').html(displayErrors(msg.responseJSON.errors));
//				console.log(msg);
				}
			});
	}		

</script>
 @endsection 
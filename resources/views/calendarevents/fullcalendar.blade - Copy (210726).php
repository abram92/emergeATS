@extends('layouts.menu')


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
 
 <div id="viewModal" class="modal fade">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="modalTitle" class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
        </div>
        <div id="modalBody" class="modal-body">
		<div id="message"></div>
		<form id="modalForm">
			<div class="form-group row">
				<label for="title" class="col-md-3 col-form-label text-md-right">Title</label>
                <div class="col-md-8">
					<span name="title" id="title"></span>
                </div>
            </div>
	@include('partials.select2_dropdown_single', ['fieldname'=>'type_id', 
									'fieldid'=>'type_id',
									'fieldlabel'=>'Type', 
									'required'=>true,
									'fieldplaceholder'=>'Choose Event Type', 
									'options'=>$alleventtypes,
									'selectedid'=>null])			
			<div class="form-group row">
				<label for="start_date" class="col-md-3 col-form-label text-md-right">Start</label>
                <div class="col-md-8">
				<div class="input-group-append">
					<span name="start_date" id="start_date"></span><span name="start_time" id="start_time"></span>
				</div>	
                </div>
            </div>	
			<div class="form-group row">
				<label for="end_date" class="col-md-3 col-form-label text-md-right">End</label>
                <div class="col-md-8">
				<div class="input-group-append">
					<span name="end_date" id="end_date"></span><span name="end_time" id="end_time"></span>
				</div>	
                </div>
            </div>	
			<div class="form-group row">
				<label for="comment" class="col-md-3 col-form-label text-md-right">Comment</label>
                <div class="col-md-8">
					<span name="comment" id="comment"></span>
                </div>
            </div>	

            <div class="form-group row">
                <label for="candidatelist" class="col-md-3 col-form-label text-md-right">Candidates</label>
                <div class="col-md-8">
					<span id="candidatelist" ></span>
				</div>	
            </div>
				
            <div class="form-group row">
                <label for="joblist" class="col-md-3 col-form-label text-md-right">Job References</label>
                <div class="col-md-8">
										<span id="joblist" ></span>

                </div>
			</div>	
				
            <div class="form-group row">
                <label for="clientlist" class="col-md-3 col-form-label text-md-right">Clients</label>
                <div class="col-md-8">
										<span id="clientlist" ></span>

                </div>
			</div>	
			
		</form>
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
        <div class="modal-header">
            <h4 id="modalTitle" class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
        </div>
        <div id="modalBody" class="modal-body">
		<div id="message"></div>
		<form id="modalForm">
			<input class="form-control" type="hidden" id="event_id">
			<div class="form-group row">
				<label for="title" class="col-md-3 col-form-label text-md-right">Title</label>
                <div class="col-md-8">
					<input placeholder="Title" class="form-control" required autofocus name="title" type="text" id="title">
                </div>
            </div>
	@include('partials.select2_dropdown_single', ['fieldname'=>'type_id', 
									'fieldid'=>'type_id',
									'fieldlabel'=>'Type', 
									'required'=>true,
									'fieldplaceholder'=>'Choose Event Type', 
									'options'=>$alleventtypes,
									'selectedid'=>null])			
			<div class="form-group row">
				<label for="start_date" class="col-md-3 col-form-label text-md-right">Start</label>
                <div class="col-md-8">
				<div class="input-group-append">
					<input class="form-control" required name="start_date" type="date" id="start_date"><input class="form-control" name="start_time" type="time" id="start_time">
				</div>	
                </div>
            </div>	
			<div class="form-group row">
				<label for="end_date" class="col-md-3 col-form-label text-md-right">End</label>
                <div class="col-md-8">
				<div class="input-group-append">
					<input class="form-control" name="end_date" type="date" id="end_date"><input class="form-control" name="end_time" type="time" id="end_time">
				</div>	
                </div>
            </div>	
			<div class="form-group row">
				<label for="comment" class="col-md-3 col-form-label text-md-right">Comment</label>
                <div class="col-md-8">
					<textarea placeholder="Comment" class="form-control" name="comment" id="comment"></textarea>
                </div>
            </div>	

            <div class="form-group row">
                <label for="candidatelist" class="col-md-3 col-form-label text-md-right">Candidates</label>
                <div class="col-md-8">
					<select id="candidatelist" class="select-select2-optional select2-fetch" multiple="" data-placeholder="Choose Candidates">
					</select>
				</div>	
            </div>
				
            <div class="form-group row">
                <label for="joblist" class="col-md-3 col-form-label text-md-right">Job References</label>
                <div class="col-md-8">
					<select id="joblist" class="select-select2-optional select2-fetch" multiple="" data-placeholder="Choose Job Ads">
					</select>
                </div>
			</div>	
				
            <div class="form-group row">
                <label for="clientlist" class="col-md-3 col-form-label text-md-right">Clients</label>
                <div class="col-md-8">
					<select id="clientlist" class="select-select2-optional select2-fetch" multiple="" data-placeholder="Choose Clients">
					</select>
                </div>
			</div>	
			
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

  <div class="container">
	<div class="row">
	<div class="col-md-4">
    <div id='external-events'>
      <h4>Draggable Events</h4>

      <div class="dropdown" id='external-events-list'>
	  <a href="#" class="dropdown-toggle btn btn-sm text-secondary fa fa-calendar" data-toggle="dropdown" title="Add Event" id="dragEvents"></a>
	  <div class='dropdown-content' aria-labelledby="dragEvents">
	  @foreach($alleventtypes as $eventtype)
        <div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
          <div class='fc-event-main'  data-id="{{ $eventtype['id'] }}" @if($eventtype['colour_hex']) style="background-color:{{ $eventtype['colour_hex'] }}" @endif>{{ $eventtype['description'] }}</div>
        </div>
		@endforeach
		</div>
      </div>

      <p>
        <input type='checkbox' id='drop-remove' />
        <label for='drop-remove'>remove after drop</label>
      </p>
    </div>
	</div>
	<div class="col-md-8">
      <h4>Filter Events</h4>
	  <div class="row">
	<div class="col-md-6">
@include('partials.select2_filter_dropdown_multiple', ['fieldid'=>'eventtype_ids', 'fieldname'=>'event_types', 'fieldlabel'=>'Event Types', 
									'fieldplaceholder'=>'Choose Event Types', 
									'options'=>$alleventtypes,
									'selectedoptions'=>old('event_types', isset($event_types) ? $event_types : null)])
	</div>
	<div class="col-md-6">
@include('partials.select2_filter_dropdown_multiple', ['fieldid'=>'consultant_ids', 'fieldname'=>'consultants', 'fieldlabel'=>'Consultants', 
									'fieldplaceholder'=>'Choose Consultants', 
									'options'=>$allconsultants,
									'selectedoptions'=>old('consultants', isset($consultants) ? $consultants : null)])
	</div>
	</div>  
	</div>
	</div>
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
    var calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
      },
	  dayMaxEvents: true,
	  navLinks: true,
      events: {
		  url : "{{ route('eventfeed') }}",
		  extraParams : function() {
			  return {
				  consultant_ids: $('#consultant_ids').val(), //document.getElementById('consultant_ids').selected,
				  event_type_ids: $('#eventtype_ids').val() //document.getElementById('eventtype_ids').selected
			  }
		  },
		  failure: function() {
			  displayMessage('Error fetching events', 'danger');
		}
	  },
	  @if (isset($publicholidays) && !empty($publicholidays)) 
	  dayRender: function(dayRenderInfo) {
		  if ($.inArray(dayRenderInfo.date, [{{ $publicholidays }}]) >= 0)
			  dayRenderInfo.addClass('publicholiday);
	  },
	  @endif
	  eventContent: function (event, element, view) {
		  let buttons = '<div class="fc-buttons">'+
					'<button class="btn btn-default edit-event" title="Edit"><i class="fa fa-pencil"></i></button>'+
					'<button class="btn btn-default remove-event" title="Delete"><i class="fa fa-trash"></i></button>'+
					'</div>';
					event.title += buttons;
//		  element.prepend(buttons);
                if (event.allDay === 'true') {
                    event.allDay = true;
                } else {
                    event.allDay = false;
                }
      },
      droppable: true, // this allows things to be dropped onto the calendar
      eventReceive: function(info) {
        // is the "remove after drop" checkbox checked?
////		console.log(info);

        if (document.getElementById('drop-remove').checked) {
          // if so, remove the element from the "Draggable Events" list
          info.draggedEl.parentNode.removeChild(info.draggedEl);
        }
		elem = info.draggedEl.innerHTML;
		var event_type = $(elem).data("id");
//	var title = $(elem).text();
		$.extend(info.event, {type_id: event_type});

		event_id = modifyEvent(info.event);
		if (event_id)
			$.extend(info.event, {id: event_id});
      },
	  selectable: true,
	  select: 	function(event) {

//console.log(event);
			$('#modalForm')[0].reset();
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
	  eventDrop:  function(info) {
  if(!confirm("Are you sure about this change?")) {
  info.revert();
}
//// console.log(info.event);

  modifyEvent(info.event);
	  },
	  eventResize:  function(info) {
  if(!confirm("Are you sure about this change?")) {
  info.revert();
}
//// console.log(info.event);
  modifyEvent(info.event);			
      },	  
	  eventClick:  function(el, jsEvent, view) {
////		  console.log(el.event.editable);
		    var start_date = (el.event.start == null) ? el.event.start : moment(el.event.start).format('YYYY-MM-DD');
			var start_time = (el.event.start == null) ? el.event.start : moment(el.event.start).format('HH:mm');
		    var end_date = (el.event.end == null) ? el.event.end : moment(el.event.end).format('YYYY-MM-DD');
			var end_time = (el.event.end == null) ? el.event.end : moment(el.event.end).format('HH:mm');
			
			var type_id = el.event.extendedProps.type_id;
			if (!type_id)
				type_id = el.event.type_id;
			$('#message').html('');
           $('#editModal #modalTitle').text('Edit Event');
		   $('#editModal button.btn-danger').css('display', 'flex');
		   $('#editModal #event_id').val(el.event.id);
           $('#editModal #title').val(el.event.title);
			$('#editModal #type_id').val(type_id);
				$('#editModal #type_id').trigger('change.select2');
			$('#editModal #start_date').val(start_date);
			$('#editModal #start_time').val(start_time);
			$('#editModal #end_date').val(end_date);
			$('#editModal #end_time').val(end_time);	
			setSelectList('#candidatelist', el.event.extendedProps.candidates);
			setSelectList('#clientlist', el.event.extendedProps.clients);
			setSelectList('#joblist', el.event.extendedProps.jobs);

            $('#editModal #modalBody').html(el.event.description);
            $('#editModal #eventUrl').attr('href',el.event.url);
            $('#editModal').modal();
        }
    });
    calendar.render();

  });


	$("document").ready(function() {

@include('scripts.ready_select2')
		
		$('#editModal button.btn-success').click(function() {
			
			let id = $('#editModal #event_id').val();
			let title = $('#editModal #title').val();
			let event_type = $('#editModal #type_id').val();
			let start = $('#editModal #start_date').val()+' '+$('#editModal #start_time').val();
			let end = $('#editModal #end_date').val()+' '+$('#editModal #end_time').val();
			let comment = $('#editModal #comment').val();
			let candidates = $('#candidatelist').val();
			let clients = $('#clientlist').val();
			let jobads = $('#joblist').val();
			
			time_start = start.trim();
			time_end = (end.trim() != '') ? end.trim() : time_start;
          let Event = {
			 _token: "{{ csrf_token() }}",  
            title: title,
            type_id: event_type,
            time_start: time_start,
            time_end: time_end,
			comment: comment,
			candidates: [candidates],
			clients: [clients],
			jobads: [jobads]
          };
		  
		  if (id == ''){
				http_method = 'POST';
				url = 'calendarevents';
		  } else {
			  $.extend(Event, { id: id } );
				http_method = 'PUT';
				url = "/calendarevents/"+id;
		  }

			$.ajax({
			type: http_method,
			url: url,
			data: Event,
			traditional:true,
			success:function(msg){
				if (msg)
					location.reload();
//				console.log(msg);

			},
			error:function(msg){
				$('#message').html(displayErrors(msg.responseJSON.errors));
//				console.log(msg);
			}
		});
		});


		$('#editModal button.btn-danger').click(function() {
			
			let id = $('#editModal #event_id').val();
		  
		  if (id == ''){

		  } else {
				http_method = 'DELETE';
				url = "/calendarevents/"+id;
	

			$.ajax({
			type: http_method,
			url: url,
			traditional:true,
			success:function(msg){
				if (msg)
					location.reload();
//				console.log(msg);

			},
			error:function(msg){
				$('#message').html(displayErrors(msg.responseJSON.errors));
//				console.log(msg);
			}
		});
		  }
		});	
	  
		$( ".select2-fetch" ).select2({
			minimumInputLength: 1,
			allowClear: true,
        ajax: { 
          url: function() {
			  return $(this).attr("id");
		  },
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              _token: "{{ csrf_token() }}",
              term: params.term, // search term
			  page: params.page || 1
            };
          },
 
          cache: true
        }

      });	  
	  

	});

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
	
	
	function modifyEvent(event) {

		var start = (event.start == null) ? event.start : moment(event.start).format('YYYY-MM-DD HH:mm:ss');
		var end = (event.end == null) ? start : moment(event.end).format('YYYY-MM-DD HH:mm:ss');
		var type_id = (event.type_id != null) ? event.type_id : event.extendedProps.type_id;

		let data = { _token: "{{ csrf_token() }}",
					"title":event.title,
					"type_id":type_id,
					"time_start":start,
					"time_end":end};
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
			traditional:true,
			success:function(msg){
				event_id = msg.insert_id;
				
			},
			error:function(msg){
				alert('Failed');
			}
		});
		
		return event_id;
	}		
		

</script>
 @endsection 
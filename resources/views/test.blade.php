@extends('layouts.menu')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h4>Autocomplete from database</h4>
                <hr>

				
               <div class="form-group">
                    <label>Candidate</label>
<select id="candidatelist" class="chosen chosen-select" multiple="" data-placeholder="Choose Candidates" style="width:200px">
</select>
                </div>
				
				               <div class="form-group">
                    <label>Job Reference</label>
<select id="joblist" class="chosen chosen-select" multiple="" data-placeholder="Choose Job Ads" style="width:200px">
</select>
                </div>
				
                <div class="form-group">
                    <label>Client</label>
<select id="clientlist" class="chosen chosen-select" multiple="" data-placeholder="Choose Clients" style="width:200px">
</select>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
@include('scripts.src_chosen')
@endpush

@section('js')
<script>
$(function () {
 //initialization of chosen select
$(".chosen-select").chosen({
  search_contains: true // an option to search between words
});
$(".chosen-select-deselect").chosen({
  allow_single_deselect: true
});

			@include('scripts.ready_chosen')
			
//ajax function to search a new value to the dropdown list
function _ajaxSearch (param, url1){
  return $.ajax({
    url: url1,
    type: "POST",
    dataType: "json",
    data: {_token: "{{ csrf_token() }}", term:param}
  })
}
//key event to call our ajax call
$(".chosen-choices input").on('keyup',function(){
  var  param = $(this).val();// get the pressed key
     if (param.length > 0) {
   selectid = $(this).closest("div").attr("id").replace('_chosen', '');

$('#'+selectid+' option').not(':selected').remove();
  _ajaxSearch(param, selectid)
  .done(function(response){
    var exists; // variable that returns a true if the value already exists in our dropdown list
    $.each(response,function(index, el) { //loop to check if the value exists inside the list


      $('#'+selectid+' option').each(function(){
        if (this.value == el.key) {
          exists = true;
        }
      });
	  
      if (!exists) {// if the value does not exists, added it to the list

        $('#'+selectid+'').append("<option value="+el.key+">"+el.value+"</option>");
      }
    });

var options = $('#'+selectid+' option');            
options.detach().sort(function(a,b) {               
    var at = $(a).text().toLowerCase();
    var bt = $(b).text().toLowerCase();         
    return (at > bt)?1:((at < bt)?-1:0);            
});
options.appendTo('#'+selectid);   

        $('#'+selectid+'').trigger("chosen:updated");//update the list
        $(this).val(param);//since the update method reset the input fill the input with the value already typed
	
  })
  alert(param);
         $(this).val(param);
}
})
});
</script>

@endsection
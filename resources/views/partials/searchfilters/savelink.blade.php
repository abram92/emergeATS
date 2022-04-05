@if (isset($query) && isset($query['search_id']) && !$isSaved)
					<div class="btn-group" role="group">
						<a class="btn bg-light" id="savebutton" href="{{ route('savedsearch.edit',$query['search_id']) }}" target="savesearch{{ $query['search_id'] }}" title="Save Search">
							<i class="fas fa-save"></i>
						</a>
					</div>
@endif
	@if ($data instanceof Illuminate\Pagination\LengthAwarePaginator)
			{{ $data->appends($query)->links() }}
	<span class="resultcount">Showing	{{ $data->firstItem() }} - {{ $data->lastItem() }} of {!! $data->total() !!} entries</span>
	@else
	<span class="resultcount">Showing 1 - {{ $data->count() }} of {!! $data->count() !!} entries</span>
	@endif		
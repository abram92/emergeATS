			<div class="input-group">
				<input class="form-control form-control-navbar" type="search" name="{{ $filterName }}" placeholder="{{ $filterPlaceholder }}" aria-label="filter" value="{{ $filterVar }}">
						<div class="input-group-append">
@if ($advancedFilter)						
							<button class="btn btn-outline-secondary bg-light" title="Advanced Filter" type="button" id="{{ $filterPrefix }}Heading" data-toggle="collapse" data-target="#{{ $filterPrefix }}Body" aria-expanded="{{ $isExpanded }}" aria-controls="{{ $filterPrefix }}Body">
								<i class="fas fa-caret-down"></i>
							</button>
@endif
@if ($canSearch)							
							<button class="btn btn-outline-secondary bg-light" name="search" value="search" title="Search" type="submit">
								<i class="fas fa-search"></i>
							</button>
@endif
@if ($canSearch && $canExport)	
<button class="btn btn-outline-secondary bg-light" disabled></button>	
@endif					
@if ($canExport)							
							<button type="submit" name="export" value="export" title="Export Result" class="btn btn-outline-secondary bg-light" >
								<i class="fas fa-file-export"></i>
							</button>
@endif	
@if (isset($canFilter) && ($canFilter))							
							<button class="btn btn-outline-secondary bg-light" name="filter" value="filter" title="Filter" type="button">
								<i class="fas fa-filter"></i>
							</button>
@endif				
						</div>
			</div>	
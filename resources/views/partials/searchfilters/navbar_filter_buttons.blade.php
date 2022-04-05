						<div class="input-group-append">
@if ($advancedFilter)						
							<button class="btn btn-outline-secondary" title="Advanced Filter" type="button" id="{{ $filterPrefix }}Heading" data-toggle="collapse" data-target="#{{ $filterPrefix }}Body" aria-expanded="{{ $isExpanded }}" aria-controls="{{ $filterPrefix }}Body">
								<i class="fas fa-caret-down"></i>
							</button>
@endif
@if ($canSearch)							
							<button class="btn btn-outline-secondary" name="search" value="search" title="Search" type="submit">
								<i class="fas fa-search"></i>
							</button>
@endif
@if ($canSearch && $canExport)	
<button class="btn btn-outline-secondary" disabled></button>	
@endif					
@if ($canExport)							
							<button type="submit" name="export" value="export" title="Export Result" class="btn btn-outline-secondary" >
								<i class="fas fa-file-export"></i>
							</button>
@endif					
						</div>
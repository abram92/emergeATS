
@php

if (!function_exists('get_badgetextcolour')) {
function get_badgetextcolour($hex) { 
  // returns brightness value from 0 to 255 
  // strip off any leading # 
  $hex = str_replace('#', '', $hex); 
  $c_r = hexdec(substr($hex, 0, 2)); 
  $c_g = hexdec(substr($hex, 2, 2)); 
  $c_b = hexdec(substr($hex, 4, 2)); 
  
  if ((($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000 > 186)
	  return 'black';
  else
	  return 'white';
  
}


}

$display_shadow = (isset($show_shadow) && $show_shadow);
	
@endphp

@if ($status !== null)
			<span class="badge1 @if ($status['colour_hex']) shadow" style="background-color:{{ $status['colour_hex'] }}; color:{{ get_badgetextcolour($status['colour_hex']) }} @elseif ($display_shadow) shadow @endif">{{ $status['description'] }}</span>
@endif

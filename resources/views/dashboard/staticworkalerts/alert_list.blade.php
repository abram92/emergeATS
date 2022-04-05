
@if ($jobapplications->count() > 0)
	<table class="alerttable">
		<tr>
	@foreach ($alertLevels as $k => $v) {
		@php $color = isset($arr[$k]) ? $v : "white"; @endphp
			<td class="alertcolumn" >
		if (isset($arr[$k])) {
			foreach ($arr[$k] as $current_row => $v1) {
				$bg_color = ( $current_row % 2 ) ? "diveven" :  "divodd";
				<div class="alertdiv @php echo $bg_color alertdiv$k">
				if (isset($v1["uploaddate"]))
					echo $v1["uploaddate"]."<br>";
				echo $v1["href"];
				if (isset($v1["company"]))
					echo "&nbsp;".$v1["company"];
				if ((isset($v1["jobtitle"])) && ($v1["jobtitle"]))
					echo "&nbsp;(".$v1["jobtitle"].")";
				if (isset($v1["jobdescr"]))
					echo "<br>".$v1["jobdescr"];

				echo "</div>";
			}
		}
		echo "</td>";
	@endforeach
		</tr>
	</table>
@endif


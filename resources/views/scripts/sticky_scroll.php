<script>
  (new IntersectionObserver(function(e,o){
    if (e[0].intersectionRatio > 0){
		$( '#sticky-anchor' ).next().removeClass( "stuck" );
    } else {
		$( '#sticky-anchor' ).next().addClass( "stuck" );
    };
})).observe(document.querySelector('#sticky-anchor'));
  </script>	
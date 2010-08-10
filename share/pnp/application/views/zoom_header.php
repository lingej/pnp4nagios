	function Gzoom (url) {
      GzoomWindow = window.open(url, "PNP", "width=<?php echo $graph_width ?>,height=<?php echo $graph_height ?>,resizable=yes,scrollbars=yes");
	  GzoomWindow.focus();
	}

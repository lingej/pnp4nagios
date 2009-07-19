<!-- Icon Box Start -->
<div class="ui-widget">
<div class="ui-widget-header ui-corner-top">
Actions
</div>
<div class="p4 ui-widget-content ui-corner-bottom" >
<script type="text/javascript">
	$(function() {
		var currentTime = new Date()
		var hours = (currentTime.getHours() * 60 * 60)
		var minutes = (currentTime.getMinutes() * 60)
		var sec = (hours + minutes)
		$("#datepicker").datepicker({showOn: 'button', buttonImage: 'media/images/calendar.png', dateFormat: '@', buttonImageOnly: true, onSelect: function(dateText, inst) { window.location.href = 'graph<?php echo $this->url ?>&end=' + (dateText / 1000 + sec) }});
	});
	</script>

<input type="hidden" id="datepicker">
<a title="PDF View" href="pdf<?php echo $this->url ?>&view=<?php echo $this->view?>"><img src="media/images/PDF_32.png"></a>
<a title="XML View" href="xml<?php echo $this->url ?>"><img src="media/images/XML_32.png"></a>

</div>
</div><p>
<!-- Icon Box End -->


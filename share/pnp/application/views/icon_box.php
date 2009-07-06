<!-- Search Box Start -->
<div class="ui-widget-header ui-corner-top">
Actions
</div>
<div class="p4 ui-widget-content ui-corner-bottom" >
<script type="text/javascript">
	$(function() {
		$("#datepicker").datepicker({showOn: 'button', buttonImage: 'media/images/calendar.png', dateFormat: '@', buttonImageOnly: true, onSelect: function(dateText, inst) { window.location.href = 'graph<?=$this->url?>&end=' + (dateText / 1000) }});
	});
	</script>

<input type="hidden" id="datepicker">
<a title="PDF View" href="pdf<?=$this->url?>"><img src="media/images/PDF_32.png"></a>
<a title="XML View" href="xml<?=$this->url?>"><img src="media/images/XML_32.png"></a>

</div><p>
<!-- Search Box End -->


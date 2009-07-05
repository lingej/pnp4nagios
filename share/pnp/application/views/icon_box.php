<!-- Search Box Start -->
<div class="ui-widget-header ui-corner-top">
Actions
</div>
<div class="p4 ui-widget-content ui-corner-bottom" >

<script type="text/javascript">
	$(function() {
		$("#datepicker").datepicker({showOn: 'button', buttonImage: '<?php echo "media/images/calendar.png";?>', buttonImageOnly: true});
	});
	</script>

<input type="hidden" id="datepicker">
<a title="PDF View" href="pdf?host=<?=$this->host?>"<img src="media/images/PDF_32.png"></a>
<a title="XML View" href="xml?host=<?=$this->host?>"<img src="media/images/XML_32.png"></a>

</div><p>
<!-- Search Box End -->


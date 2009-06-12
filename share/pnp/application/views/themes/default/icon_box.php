<!-- Search Box Start -->
<div class="ui-widget-header">
Acrions
</div>
<div class="p4 ui-widget-content ui-corner-bottom" >

<script type="text/javascript">
	$(function() {
		$("#datepicker").datepicker({showOn: 'button', buttonImage: '<?php echo "application/views/".$this->theme_path."images/calendar.png";?>', buttonImageOnly: true});
	});
	</script>

<input type="hidden" id="datepicker">
<a title="PDF View" href="pdf?host=<?=$this->host?>"<img src="<?=$this->theme_url?>images/PDF_32.png"></a>
<a title="XML View" href="xml?host=<?=$this->host?>"<img src="<?=$this->theme_url?>images/XML_32.png"></a>

</div><p>
<!-- Search Box End -->


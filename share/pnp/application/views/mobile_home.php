<div data-role="content">
<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="a">
<li><a href="<?php echo url::base(TRUE)?>mobile/overview" data-transition="pop"><?php echo Kohana::lang('common.mobile-all-hosts')?></a></li>
<li><a href="<?php echo url::base(TRUE)?>mobile/search" data-transition="pop"><?php echo Kohana::lang('common.mobile-search-hosts')?></a></li>
<?php
if($this->data->getFirstPage() && $this->isAuthorizedFor('pages') ){
    echo "<li><a href=\"".url::base(TRUE)."mobile/pages\" data-transition=\"pop\">".Kohana::lang('common.mobile-pages')."</a></li>"; 
}

if($this->data->getFirstSpecialTemplate() ){
    echo "<li><a href=\"".url::base(TRUE)."mobile/special\" data-transition=\"pop\">".Kohana::lang('common.mobile-special-templates')."</a></li>"; 
}
?>
<li><a href="<?php echo url::base(TRUE)?>mobile/graph/.pnp-internal/runtime" data-transition="pop"><?php echo Kohana::lang('common.mobile-statistics')?></a></li>
<li><a href="<?php echo url::base(TRUE)?>mobile/go/classic" data-ajax="false" data-transition="pop"><?php echo Kohana::lang('common.mobile-go-classic')?></a></li>
</ul>
</div><!-- /content -->

{assign var="styleCommon" value="installCommon"}
{include file="common/header.tpl"}

<div id="main">
<div id="contenu">
<div id="logo">
{include file="common/langs_selection.tpl"}
<img src="{$DIR_PLUGIN_PHPMV}/themes/default/images/install.png" alt="phpMyVisites" width="281" height="105" />
</div>
<div class="both"></div>
<div id="generalInstall">
{include file="install/all_steps.tpl"}
</div>
<div id="detailInstall">
{include file="$contentpage"}
{if $show_next_step}
	<p class="next_step">
		<a href="./?exec=phpmv&mod={$next_module_name}">{'install_next_step'|translate}</a>
	</p>
{/if}
</div>
<div class="both"></div>
<h3>{'install_status'|translate}</h3>
<div id="instalpercent">
<p style="width: {$percent_done}%;"></p>
</div>
{'install_done'|translate:$percent_done}
</div>
{include file="common/footer.tpl"}
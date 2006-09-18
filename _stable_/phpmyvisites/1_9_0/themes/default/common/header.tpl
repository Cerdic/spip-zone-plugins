{$SPIP_DEBUT_PAGE}
  <link href="{$DIR_PLUGIN_PHPMV}/themes/default/css/{if $styleCommon}{$styleCommon}{else}interfaceCommon{/if}.css" rel="stylesheet" type="text/css" />	
	<link href="{$DIR_PLUGIN_PHPMV}/themes/default/css/styles.php?dir={'text_dir'|translate}" rel="stylesheet" type="text/css" />	
	<script type="text/javascript" src="{$DIR_PLUGIN_PHPMV}/themes/default/include/menu.js"></script>
	<script type="text/javascript" src="{$DIR_PLUGIN_PHPMV}/themes/default/include/misc.js"></script>
	<link rel="alternate" type="application/rss+xml" title="RSS" href="./?exec=phpmv&mod=view_rss&amp;rss_hash={$rss_hash}" />

<!-- http://www.phpmyvisites.net/ -->	
{if $header_error_message}
	<h2>
	{$header_error_message}	
	{if $mod=="admin"} {'admin_db_log'|translate} {/if}
	</h2>
{/if}
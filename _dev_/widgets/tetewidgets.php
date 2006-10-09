<?php
/* insert le css et le js externes pour Widgets dans le <head> (#INSERT_HEAD)
 *
 *  Widgets plugin for spip (c) Fil 2006 -- licence GPL
 */

function Widgets_insert_head($flux)
{
	$jsFile = find_in_path('widgets.js');
	$cssFile = find_in_path('widgets.css');
	$imgPath = dirname(find_in_path('images/edit.gif'));

	$txtEditer = addslashes(html2unicode(_T(
		'widgets:editer')));
	$txtErrInterdit = addslashes(unicode_to_javascript(html2unicode(_T(
		'widgets:erreur_ou_interdit'))));

	$incHead = <<<EOH

<link rel="stylesheet" href="$cssFile" type="text/css" media="all" />
<script src="{$jsFile}" type="text/javascript"></script>
<script type="text/javascript">
	var configWidgets = new configWidgets({
		'imgPath':'{$imgPath}',
		'txtEditer':'{$txtEditer}',
		'txtErrInterdit':'{$txtErrInterdit}'
	});
</script >
EOH;

	return $flux . $incHead;
}
?>

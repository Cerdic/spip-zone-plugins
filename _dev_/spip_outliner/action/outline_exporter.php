<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('public/assembler');
function action_outline_exporter_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_form = _request('id_form');

	$out = recuperer_fond('outline.opml',array('id_table'=>intval($id_form)));
	//$out = preg_replace(",\n[\s]*(?=\n),","",$out);
	
	$filename=str_replace(":","_",$arg);
	if (preg_match(",<tit[rl]e>(.*)</tit[rl]e>,Uims",$out,$regs))
		$filename = preg_replace(',[^-_\w]+,', '_', trim(translitteration(textebrut(typo($regs[1])))));
	$extension = "opml";
	
	Header("Content-Type: text/xml; charset=".$GLOBALS['meta']['charset']);
	Header("Content-Disposition: attachment; filename=$filename.$extension");
	Header("Content-Length: ".strlen($out));
	echo $out;
	exit();
}

?>
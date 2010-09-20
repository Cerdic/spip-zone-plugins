<?php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * pipeline insert_head
 * @param unknown_type $flux
 * @return unknown_type
 */
function gins_header_prive($flux) {

	// incorporer les CSS et JS si concerné.
	// Sinon, retour direct.
	
	if(!($e = _request('exec'))
		|| ($e != 'get_infos_spip'))
	{
		return($flux);
	}

	$css_code = $js_code = $result = '';
	
	// inclure le css dans la page
	if($f = find_in_path('gins_prive.css'))
	{
		$css_code .= trim(file_get_contents($f));
		
		$result .= <<<EOS
<style type="text/css">
<!--
$css_code
-->
</style>

EOS;

	}
	
	// inclure le js dans la page
	if($f = find_in_path('gins_prive.js'))
	{
		$js_code .= trim(file_get_contents($f));
		
		$result .= <<<EOS
<script type="text/javascript">
//<![CDATA[
$js_code
//]]>
</script>

EOS;
	}
		
	$flux .= PHP_EOL . '<!-- gins -->' . PHP_EOL . $result . PHP_EOL;

	return ($flux);
}


<?php

# dereferencer les URLs de tracking de feedburner
function pivotsyndic_feedburner($flux) {
	if (strstr($flux, '<feedburner:origLink>'))
		$flux =  preg_replace(',(<item>.*)<link>.*</link>(.*)<feedburner:origLink>(.*)<.*>,Uims',
		'\1\2<link>\3</link>', $flux);

	return $flux;
}
?>
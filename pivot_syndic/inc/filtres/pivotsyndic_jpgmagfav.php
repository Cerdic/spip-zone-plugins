<?php
function pivotsyndic_jpgmagfav($flux) {
  if (preg_match(',<link>http://www.jpgmag.com/people/(.*)/favorites</link>,Uims', $flux)) {
    $flux = preg_replace('/<media:credit role="photographer">(.*)<\/media:credit>/', '<author>\1</author>', $flux);
  }
	return $flux;
}
?>
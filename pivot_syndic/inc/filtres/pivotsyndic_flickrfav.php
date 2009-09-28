<?php
function pivotsyndic_flickrfav($flux) {
  if (preg_match(',<link>http://www.flickr.com/photos/(.*)/favorites/</link>,Uims', $flux)) {
    $flux = preg_replace('/<author flickr:profile="(.*)">nobody@flickr.com \((.*)\)<\/author>/', '<author><a href="\1">\2</a></author>', $flux);
  }
	return $flux;
}
?>
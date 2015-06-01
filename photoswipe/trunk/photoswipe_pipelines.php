<?php

function photoswipe_insert_head_css($flux) {

	return $flux . "<style type='text/css'>
	img[data-photo].photoshow { cursor: zoom-in; }
	</style>
	";
}

function photoswipe_insert_head($flux){
	$flux = photoswipe_insert_head_css($flux); // au cas ou il n'est pas implemente

	$flux .='
<script src="'.(find_in_path('photoswipe_insert.js')).'" type="text/javascript"></script>
<script type="text/javascript">
// configuration
photoswipe = {
  path: "' . find_in_path('lib/photoswipe/'). '/",
  selector: "img[data-photo],a[type]",
  gallery: true, // galerie
  debug: true, // debug
};
$(function() {
    photoswipe_init();
    if (!!$.fn.on) {
      $(document).on("mouseover", photoswipe.selector, photoshow_hover);
      $(document).on("click", photoswipe.selector, photoshow);
    } else if (!!$.fn.live) {
      $(photoswipe.selector).live("mouseover", photoshow_hover);
      $(photoswipe.selector).live("click", photoshow);
    }
});
</script>
';
	return $flux;
}

function photoswipe_post_propre($texte) {
	return filtre_photoswipe_preparer($texte);
}

function filtre_photoswipe_preparer($texte) {
	foreach (extraire_balises($texte, 'img') as $img) {
		if ($src = extraire_attribut($img, 'src')
		AND !extraire_attribut($img, 'data-photo')
		) {
			$l = largeur($img);
			$h = hauteur($img);

			if ($l > 500 OR $h > 300) {

	// pour echapper à la ligne de filtres_images_lib_mini qui remplace tout:
	// `$tag = str_replace($src,$surcharge['src'],$tag);`

				$photo_src = str_replace('.', '__.__', $src);
				$img2 = inserer_attribut($img, 'data-photo', $photo_src);
				$img2 = inserer_attribut($img2, 'data-photo-w', $l);
				$img2 = inserer_attribut($img2, 'data-photo-h', $h);
				$texte = str_replace($img, $img2, $texte);
			}
		}
	}
	return $texte;
}

?>

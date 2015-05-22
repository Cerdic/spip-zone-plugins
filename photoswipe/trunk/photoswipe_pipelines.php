<?php

function photoswipe_insert_head_css($flux) {

	return $flux . "<style type='text/css'>
	img[data-photo-src] { cursor: zoom-in; }
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
  gallery: true, // galerie
  debug: true, // galerie
};
$(function() {
    photoswipe_init();
    if (!!$.fn.on) {
      $(document).on("click", "img[data-photo-src]", photoshow);
    } else if (!!$.fn.live) {
      $("img[data-photo-src]").live("click", photoshow);
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
		AND !extraire_attribut($img, 'data-photosrc')
		) {
			$l = largeur($img);
			$h = hauteur($img);

			if ($l > 500 OR $h > 300) {

	// pour echapper à la ligne de filtres_images_lib_mini qui remplace tout:
	// `$tag = str_replace($src,$surcharge['src'],$tag);`

				$photo_src = str_replace('.', '__.__', $src);
				$img2 = inserer_attribut($img, 'data-photo-src', $photo_src);
				$img2 = inserer_attribut($img2, 'data-photo-w', $l);
				$img2 = inserer_attribut($img2, 'data-photo-h', $h);
				$texte = str_replace($img, $img2, $texte);
			}
		}
	}
	return $texte;
}

?>

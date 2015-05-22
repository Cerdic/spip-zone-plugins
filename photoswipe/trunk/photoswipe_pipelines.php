<?php

function photoswipe_insert_head($flux){
	//$flux = photoswipe_insert_head_css($flux); // au cas ou il n'est pas implemente

	$flux .='
<script src="'.(find_in_path('photoswipe_insert.js')).'" type="text/javascript"></script>
<script type="text/javascript">
// configuration
photoswipe = {
  path: "' . find_in_path('lib/photoswipe/'). '/",
  gallery: false, // galerie bugguee
};
$(function() {
    photoswipe_init();
    $("img.photoshow").live("click", photoshow);
});
</script>
';
	return $flux;
}


function filtre_photoswipe_preparer($texte) {
	foreach (extraire_balises($texte, 'img') as $img) {
		if ($src = extraire_attribut($img, 'src')) {
			// pour echapper à la ligne de filtres_images_lib_mini qui remplace tout :
			// `$tag = str_replace($src,$surcharge['src'],$tag);`

			$photo_src = str_replace('.', '__.__', $src);
			$img2 = inserer_attribut($img, 'data-photo-src', $photo_src);
			$img2 = inserer_attribut($img2, 'data-photo-w', largeur($img));
			$img2 = inserer_attribut($img2, 'data-photo-h', hauteur($img));
			$texte = str_replace($img, $img2, $texte);
		}
	}
	return $texte;
}

?>

<?php
/**
 * Fonction du plugin Bolo
 *
 * @plugin     Bolo
 * @copyright  2010
 * @author     Cyril MARION - Ateliers CYM
 * @licence    GPL
 * @package    SPIP\Bolo\Fonctions
 */

/**
 * Retourne du texte en latin.
 *
 * @return string
 */
function bolo_latin_dist() {

	$texte = "Nam id pede vel ipsum pulvinar pretium. Mauris id nunc. Vivamus lorem. Proin auctor rutrum ligula. Sed suscipit justo et nunc. Praesent ut leo quis neque luctus eleifend. Vestibulum nec nisl. Proin tincidunt. Sed enim. Curabitur posuere purus a quam. Aenean odio wisi, vestibulum sed, accumsan vitae, rhoncus suscipit, lectus. Sed a lacus. Aenean erat odio, molestie a, lobortis ut, blandit eu, arcu. Donec mauris. Sed sed libero ac sem venenatis sollicitudin. Donec arcu est, volutpat id, dictum a, molestie eu, justo. Nam aliquet faucibus quam. Pellentesque cursus, neque eu placerat facilisis, metus ante fringilla mi, vitae vestibulum nulla turpis quis orci. Quisque nec turpis vel justo volutpat venenatis. Mauris fermentum. Nulla blandit, augue a laoreet gravida, velit lectus molestie wisi, eget volutpat velit eros sit amet tortor. Suspendisse sollicitudin lectus. Nunc velit mauris, ultrices vel, vestibulum et, rhoncus sed, massa. Curabitur luctus erat ac dolor. In pulvinar posuere sapien. Suspendisse dapibus elementum quam. Ut nec diam. Nulla pulvinar. Nam id pede vel ipsum pulvinar pretium. Mauris id nunc. Vivamus lorem. Proin auctor rutrum ligula. Sed suscipit justo et nunc. Praesent ut leo quis neque luctus eleifend. Vestibulum nec nisl. Proin tincidunt. Sed enim. Curabitur posuere purus a quam. Aenean odio wisi, vestibulum sed, accumsan vitae, rhoncus suscipit, lectus. Sed a lacus. Aenean erat odio, molestie a, lobortis ut, blandit eu, arcu. Donec mauris. Sed sed libero ac sem venenatis sollicitudin. Donec arcu est, volutpat id, dictum a, molestie eu, justo. Nam aliquet faucibus quam. Pellentesque cursus, neque eu placerat facilisis, metus ante fringilla mi, vitae vestibulum nulla turpis quis orci. Quisque nec turpis vel justo volutpat venenatis. Mauris fermentum. Nulla blandit, augue a laoreet gravida, velit lectus molestie wisi, eget volutpat velit eros sit amet tortor. Suspendisse sollicitudin lectus. Nunc velit mauris, ultrices vel, vestibulum et, rhoncus sed, massa. Curabitur luctus erat ac dolor. In pulvinar posuere sapien. Suspendisse dapibus elementum quam. Ut nec diam. Nulla pulvinar.";
	$texte_aleatoire = filtrer('decaler_texte', $texte);

	return $texte_aleatoire;
}

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
 * Retourne du texte en latin entremêlé de termes « gangsta rap » (en anglais).
 *
 * @link http://lorizzle.nl/?feed=1
 * @return string
 */
function bolo_gangsta_dist() {

	$texte = "Lorizzle ma nizzle fo bizzle amizzle, pizzle adipiscing ghetto. Nullam sapizzle velizzle, sheezy volutpizzle, fo shizzle my nizzle sheezy, own yo' vel, arcu. Pellentesque crazy shut the shizzle up. Go to hizzle erizzle. Dizzle black dolor dapibizzle phat tempizzle tempor. Mauris pellentesque nibh fo shizzle turpizzle. izzle pot. Yo mamma shiz shit things. Black i'm in the shizzle fizzle platea dictumst. Shizzle my nizzle crocodizzle dapibizzle. Curabitur tellizzle pot, sizzle fizzle, yo fo shizzle, pizzle vitae, nunc. That's the shizzle suscipizzle. Integer semper break it down sed purus. Etizzle laoreet ghetto tellivizzle nisl. Sed quis arcu. Check it out bow wow wow, ipsum hizzle malesuada scelerisque, nulla hizzle mah nizzle bizzle, for sure luctus doggy nulla i saw beyonces tizzles and my pizzle went crizzle hizzle. Vivamizzle ullamcorper, tortor mammasay mammasa mamma oo sa varizzle ghetto, nibh nunc fo turpis, izzle we gonna chung leo elit izzle dolizzle. Mauris aliquet, orci ghetto mofo mammasay mammasa mamma oo sa, sem away luctizzle shiznit, izzle bibendizzle yippiyo pimpin' izzle nisl. Nullam a velizzle izzle orci eleifend viverra. Phasellus rizzle nibh. Curabitizzle nizzle my shizz mah nizzle pede izzle facilisizzle. Mah nizzle sapizzle shiznit, tellivizzle dawg, molestie sed, egestizzle a, erizzle. Nulla vitae turpis away nibh bibendizzle go to hizzle. Gangster pulvinar consectetuer pot. Aliquam rizzle bling bling. Nunc izzle shiz at lectus pretizzle faucibizzle. Crizzle crunk lacizzle tellivizzle dui condimentizzle ultricies. Ut nisl. Izzle gangster urna. Integer bow wow wow ipsizzle things boofron. Donizzle izzle yo.";
	$texte_aleatoire = filtrer('decaler_texte', $texte);

	return $texte_aleatoire;
}

<?php
/**
 * Test unitaire de la fonction recuperer_passage_lire
 * du fichier ../plugins/spip-bible/traduction/lire.php
 *
 * genere automatiquement par TestBuilder
 * le 2010-03-04 00:14
 */
    global $spip_lang;
    $spip_lang = 'fr';
	$test = 'recuperer_passage_lire';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/spip-bible/traduction/lire.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('recuperer_passage_lire', essais_recuperer_passage_lire());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function essais_recuperer_passage_lire(){
		$essais = array (
  0 => 
  array (
    0 => '<strong>1</strong><sup>2 </sup> La terre était un chaos, elle était vide&nbsp;; il y avait des ténèbres au-dessus de l\'abîme, et le souffle de Dieu tournoyait
      au-dessus des eaux. <br /><sup>3 </sup> Dieu dit&nbsp;: Qu\'il y ait de la lumière&nbsp;! Et il y eut de la lumière. <br /><sup>4 </sup> Dieu vit que la lumière était bonne, et Dieu sépara la lumière et les ténèbres. <br /><sup>5 </sup> Dieu appela la lumière «&nbsp;jour&nbsp;», et il appela les ténèbres «&nbsp;nuit&nbsp;». Il y eut un soir et il y eut un matin&nbsp;: premier jour.
      <br /><sup>6 </sup> Dieu dit&nbsp;: Qu\'il y ait une voûte au milieu des eaux pour séparer les eaux des eaux&nbsp;! <br /><sup>7 </sup> Dieu fit la voûte&nbsp;; il sépara les eaux qui sont au-dessous de la voûte et les eaux qui sont au-dessus de la voûte. Il en fut
      ainsi. <br /><sup>8 </sup> Dieu appela la voûte «&nbsp;ciel&nbsp;». Il y eut un soir et il y eut un matin&nbsp;: deuxième jour. <br /><sup>9 </sup> Dieu dit&nbsp;: Que les eaux qui sont au-dessous du ciel s\'amassent en un seul lieu, et que la terre ferme apparaisse&nbsp;! Il en
      fut ainsi. <br /><sup>10 </sup> Dieu appela la terre ferme «&nbsp;terre&nbsp;», et il appela la masse des eaux «&nbsp;mer&nbsp;». Dieu vit que cela était bon. <br /><sup>11 </sup> Dieu dit&nbsp;: Que la terre donne de la verdure, de l\'herbe porteuse de semence, des arbres fruitiers qui portent sur la terre
      du fruit selon leurs espèces et qui ont en eux leur semence&nbsp;! Il en fut ainsi. <br /><sup>12 </sup> La terre produisit de la verdure, de l\'herbe porteuse de semence selon ses espèces et des arbres qui portent du fruit et
      qui ont en eux leur semence selon leurs espèces. Dieu vit que cela était bon. <br /><sup>13 </sup> Il y eut un soir et il y eut un matin&nbsp;: troisième jour. <br /><sup>14 </sup> Dieu dit&nbsp;: Qu\'il y ait des luminaires dans la voûte céleste pour séparer le jour et la nuit&nbsp;! Qu\'ils servent de signes pour
      marquer les rencontres festives, les jours et les années, <br /><sup>15 </sup> qu\'ils servent de luminaires dans la voûte céleste pour éclairer la terre&nbsp;! Il en fut ainsi. <br /><sup>16 </sup> Dieu fit les deux grands luminaires, le grand luminaire pour dominer le jour et le petit luminaire pour dominer la nuit, ainsi
      que les étoiles. <br /><sup>17 </sup> Dieu les plaça dans la voûte céleste pour éclairer la terre, <br /><sup>18 </sup> pour dominer le jour et la nuit, et pour séparer la lumière et les ténèbres. Dieu vit que cela était bon. <br /><sup>19 </sup> Il y eut un soir et il y eut un matin&nbsp;: quatrième jour. <br /><sup>20 </sup> Dieu dit&nbsp;: Que les eaux grouillent de petites bêtes, d\'êtres vivants, et que des oiseaux volent au-dessus de la terre, face
      à la voûte céleste&nbsp;! <br /><sup>21 </sup> Dieu créa les grands monstres marins et tous les êtres vivants qui fourmillent, dont les eaux se mirent à grouiller, selon
      leurs espèces, ainsi que tout oiseau selon ses espèces. Dieu vit que cela était bon. <br /><sup>22 </sup> Dieu les bénit en disant&nbsp;: Soyez féconds, multipliez-vous et remplissez les eaux des mers&nbsp;; et que les oiseaux se multiplient
      sur la terre&nbsp;! <br /><sup>23 </sup> Il y eut un soir et il y eut un matin&nbsp;: cinquième jour. <br /><sup>24 </sup> Dieu dit&nbsp;: Que la terre produise des êtres vivants selon leurs espèces&nbsp;: bétail, bestioles, animaux sauvages, chacun selon
      ses espèces&nbsp;! Il en fut ainsi. <br /><sup>25 </sup> Dieu fit les animaux sauvages selon leurs espèces, le bétail selon son espèce, et toutes les bestioles de la terre selon leur
      espèce. Dieu vit que cela était bon. <br /><sup>26 </sup> Dieu dit&nbsp;: Faisons les humains à notre image, selon notre ressemblance, pour qu\'ils dominent sur les poissons de la mer,
      sur les oiseaux du ciel, sur le bétail, sur toute la terre et sur toutes les bestioles qui fourmillent sur la terre. <br /><sup>27 </sup> Dieu créa les humains à son image&nbsp;: il les créa à l\'image de Dieu&nbsp;; homme et femme il les créa. <br /><sup>28 </sup> Dieu les bénit&nbsp;; Dieu leur dit&nbsp;: Soyez féconds, multipliez-vous, remplissez la terre et soumettez-la. Dominez sur les poissons
      de la mer, sur les oiseaux du ciel et sur tous les animaux qui fourmillent sur la terre. <br /><sup>29 </sup> Dieu dit&nbsp;: Je vous donne toute herbe porteuse de semence sur toute la terre, et tout arbre fruitier porteur de semence&nbsp;; ce
      sera votre nourriture. <br /><sup>30 </sup> A tout animal de la terre, à tout oiseau du ciel, à tout ce qui fourmille sur la terre et qui a souffle de vie, je donne toute
      herbe verte pour nourriture. Il en fut ainsi. <br /><sup>31 </sup> Dieu vit alors tout ce qu\'il avait fait&nbsp;: c\'était très bon. Il y eut un soir et il y eut un matin&nbsp;: le sixième jour.  		<br /><strong>2</strong><sup>1 </sup> Ainsi furent achevés le ciel et la terre, et toute leur armée. <br /><sup>2 </sup> Le septième jour, Dieu avait achevé tout le travail qu\'il avait fait&nbsp;; le septième jour, il se reposa de tout le travail
      qu\'il avait fait. <br /><sup>3 </sup> Dieu bénit le septième jour et en fit un jour sacré, car en ce jour Dieu se reposa de tout le travail qu\'il avait fait en
      créant. 
<br />',
    1 => 'Gn',
    2 => '1',
    3 => '2',
    4 => '2',
    5 => '3',
    6 => 'NBS',
    7 => 'fr',
  ),
);
		return $essais;
	}











?>
<?php
/**
 * Test unitaire de la fonction recuperer_passage_gateway
 * du fichier ../plugins/spip-bible/traduction/gateway.php
 *
 * genere automatiquement par TestBuilder
 * le 2010-03-04 22:33
 */

	$test = 'recuperer_passage_gateway';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/spip-bible/traduction/gateway.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('recuperer_passage_gateway', essais_recuperer_passage_gateway());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function essais_recuperer_passage_gateway(){
		$essais = array (
  0 => 
  array (
    0 => '<strong>1</strong><sup>2 </sup>La terre était informe et vide: il y avait des ténèbres à la surface de l\'abîme, et l\'esprit de Dieu se mouvait au-dessus des eaux.<br />  <sup>3 </sup>Dieu dit: Que la lumière soit! Et la lumière fut.<br />  <sup>4 </sup>Dieu vit que la lumière était bonne; et Dieu sépara la lumière d\'avec les ténèbres.<br />  <sup>5 </sup>Dieu appela la lumière jour, et il appela les ténèbres nuit. Ainsi, il y eut un soir, et il y eut un matin: ce fut le premier jour.<br />  <sup>6 </sup>Dieu dit: Qu\'il y ait une étendue entre les eaux, et qu\'elle sépare les eaux d\'avec les eaux.<br />  <sup>7 </sup>Et Dieu fit l\'étendue, et il sépara les eaux qui sont au-dessous de l\'étendue d\'avec les eaux qui sont au-dessus de l\'étendue. Et cela fut ainsi.<br />  <sup>8 </sup>Dieu appela l\'étendue ciel. Ainsi, il y eut un soir, et il y eut un matin: ce fut le second jour.<br />  <sup>9 </sup>Dieu dit: Que les eaux qui sont au-dessous du ciel se rassemblent en un seul lieu, et que le sec paraisse. Et cela fut ainsi.<br />  <sup>10 </sup>Dieu appela le sec terre, et il appela l\'amas des eaux mers. Dieu vit que cela était bon.<br />  <sup>11 </sup>Puis Dieu dit: Que la terre produise de la verdure, de l\'herbe portant de la semence, des arbres fruitiers donnant du fruit selon leur espèce et ayant en eux leur semence sur la terre. Et cela fut ainsi.<br />  <sup>12 </sup>La terre produisit de la verdure, de l\'herbe portant de la semence selon son espèce, et des arbres donnant du fruit et ayant en eux leur semence selon leur espèce. Dieu vit que cela était bon.<br />  <sup>13 </sup>Ainsi, il y eut un soir, et il y eut un matin: ce fut le troisième jour.<br />  <sup>14 </sup>Dieu dit: Qu\'il y ait des luminaires dans l\'étendue du ciel, pour séparer le jour d\'avec la nuit; que ce soient des signes pour marquer les époques, les jours et les années;<br />  <sup>15 </sup>et qu\'ils servent de luminaires dans l\'étendue du ciel, pour éclairer la terre. Et cela fut ainsi.<br />  <sup>16 </sup>Dieu fit les deux grands luminaires, le plus grand luminaire pour présider au jour, et le plus petit luminaire pour présider à la nuit; il fit aussi les étoiles.<br />  <sup>17 </sup>Dieu les plaça dans l\'étendue du ciel, pour éclairer la terre,<br />  <sup>18 </sup>pour présider au jour et à la nuit, et pour séparer la lumière d\'avec les ténèbres. Dieu vit que cela était bon.<br />  <sup>19 </sup>Ainsi, il y eut un soir, et il y eut un matin: ce fut le quatrième jour.<br />  <sup>20 </sup>Dieu dit: Que les eaux produisent en abondance des animaux vivants, et que des oiseaux volent sur la terre vers l\'étendue du ciel.<br />  <sup>21 </sup>Dieu créa les grands poissons et tous les animaux vivants qui se meuvent, et que les eaux produisirent en abondance selon leur espèce; il créa aussi tout oiseau ailé selon son espèce. Dieu vit que cela était bon.<br />  <sup>22 </sup>Dieu les bénit, en disant: Soyez féconds, multipliez, et remplissez les eaux des mers; et que les oiseaux multiplient sur la terre.<br />  <sup>23 </sup>Ainsi, il y eut un soir, et il y eut un matin: ce fut le cinquième jour.<br />  <sup>24 </sup>Dieu dit: Que la terre produise des animaux vivants selon leur espèce, du bétail, des reptiles et des animaux terrestres, selon leur espèce. Et cela fut ainsi.<br />  <sup>25 </sup>Dieu fit les animaux de la terre selon leur espèce, le bétail selon son espèce, et tous les reptiles de la terre selon leur espèce. Dieu vit que cela était bon.<br />  <sup>26 </sup>Puis Dieu dit: Faisons l\'homme à notre image, selon notre ressemblance, et qu\'il domine sur les poissons de la mer, sur les oiseaux du ciel, sur le bétail, sur toute la terre, et sur tous les reptiles qui rampent sur la terre.<br />  <sup>27 </sup>Dieu créa l\'homme à son image, il le créa à l\'image de Dieu, il créa l\'homme et la femme.<br />  <sup>28 </sup>Dieu les bénit, et Dieu leur dit: Soyez féconds, multipliez, remplissez la terre, et l\'assujettissez; et dominez sur les poissons de la mer, sur les oiseaux du ciel, et sur tout animal qui se meut sur la terre.<br />  <sup>29 </sup>Et Dieu dit: Voici, je vous donne toute herbe portant de la semence et qui est à la surface de toute la terre, et tout arbre ayant en lui du fruit d\'arbre et portant de la semence: ce sera votre nourriture.<br />  <sup>30 </sup>Et à tout animal de la terre, à tout oiseau du ciel, et à tout ce qui se meut sur la terre, ayant en soi un souffle de vie, je donne toute herbe verte pour nourriture. Et cela fut ainsi.<br />  <sup>31 </sup>Dieu vit tout ce qu\'il avait fait et voici, cela était très bon. Ainsi, il y eut un soir, et il y eut un matin: ce fut le sixième jour.<br /> <br /><strong>2</strong><sup>1 </sup>Ainsi furent achevés les cieux et la terre, et toute leur armée.<br />  <sup>2 </sup>Dieu acheva au septième jour son oeuvre, qu\'il avait faite: et il se reposa au septième jour de toute son oeuvre, qu\'il avait faite.<br />',
    1 => 'Gn',
    2 => 1,
    3 => 2,
    4 => 2,
    5 => 2,
    6 => 
    array (
      0 => 2,
      1 => 'LSG',
    ),
    7 => 'fr',
  ),
  1 => 
  array (
    0 => '<strong>6</strong><sup>1 </sup> Quand les hommes commencèrent à se multiplier sur la terre et qu\'ils eurent des filles,<br />  <sup>2 </sup> les fils de Dieu virent que les filles des hommes étaient belles, et ils prirent pour femmes celles qu\'ils choisirent parmi elles.<br />  <sup>3 </sup> Alors l\'Eternel dit:<br />---Mon Esprit ne va pas lutter indéfiniment avec les hommes, à cause de leurs fautes. Ce sont des êtres dominés par leurs faiblesses. Je leur donne encore cent vingt ans à vivre.<br /><sup>4 </sup> A cette époque-là, il y avait des géants sur la terre, et aussi après que les fils de Dieu se furent unis aux filles des hommes et qu\'elles leur eurent donné des enfants. Ce sont ces héros si fameux d\'autrefois.<br /><sup>5 </sup> L\'Eternel vit que les hommes faisaient de plus en plus de mal sur la terre: à longueur de journée, leur cur ne concevait que le mal.<br />  <sup>6 </sup> Alors l\'Eternel fut peiné d\'avoir créé l\'homme sur la terre, et il en eut le cur très affligé.<br />  <sup>7 </sup> Il dit alors:<br />---Je supprimerai de la surface de la terre les hommes que j\'ai créés. Oui, j\'exterminerai les hommes et les animaux jusqu\'aux bêtes qui se meuvent à ras de terre et aux oiseaux du ciel, car je regrette de les avoir faits.<br />  <sup>8 </sup> Mais Noé obtint la faveur de l\'Eternel.<br /> ',
    1 => 'Gn',
    2 => 6,
    3 => 1,
    4 => 6,
    5 => 8,
    6 => 
    array (
      0 => 32,
      1 => 'BDS',
    ),
    7 => 'fr',
  ),
);
		return $essais;
	}



?>
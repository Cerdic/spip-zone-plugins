<?php
/**
 * Test unitaire de la fonction bible_supprimer_retour
 * du fichier ../plugins/spip-bible/bible_fonctions.php
 *
 * genere automatiquement par TestBuilder
 * le 2010-03-04 22:49
 */

	$test = 'bible_supprimer_retour';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/spip-bible/bible_fonctions.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('bible_supprimer_retour', essais_bible_supprimer_retour());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function essais_bible_supprimer_retour(){
		$essais = array (
  0 => 
  array (
    0 => '<quote>
<strong>22</strong><sup>1 </sup>
Du maître de chant. Sur »la biche de l\'aurore.» Psaume. De David. Mon Dieu, mon Dieu, pourquoi m\'as-tu abandonné? Loin de me sauver, les paroles que je rugis!<br />
<sup>2 </sup>
 Mon Dieu, le jour j\'appelle et tu ne réponds pas, la nuit, point de silence pour moi.<br />
<sup>3 </sup>
 Et toi, le Saint, qui habites les louanges d\'Israël!<br />
<sup>4 </sup>
 en toi nos pères avaient confiance, confiance, et tu les délivrais,<br />
<sup>5 </sup>
 vers toi ils criaient, et ils échappaient, en toi leur confiance, et ils n\'avaient pas honte.<br />
<sup>6 </sup>
 Et moi, ver et non pas homme, risée des gens, mépris du peuple,<br />
<sup>7 </sup>
 tous ceux qui me voient me bafouent, leur bouche ricane, ils hochent la tête<br />
<sup>8 </sup>
 «Il s\'est remis à Yahvé, qu\'il le délivre! qu\'il le libère, puisqu\'il est son ami!»<br />
<sup>9 </sup>
 C\'est toi qui m\'as tiré du ventre, ma confiance près des mamelles de ma mère;<br />
<sup>10 </sup>
 sur toi je fus jeté au sortir des entrailles; dès le ventre de ma mère, mon Dieu c\'est toi.<br />
<sup>11 </sup>
 Ne sois pas loin : proche est l\'angoisse, point de secours!<br />
<sup>12 </sup>
 Des taureaux nombreux me cernent, de fortes bêtes de Bashân m\'encerclent;<br />
<sup>13 </sup>
 contre moi bâille leur gueule, lions lacérant et rugissant.<br />
<sup>14 </sup>
 Comme l\'eau je m\'écoule et tous mes os se disloquent; mon cœur est pareil à la cire, il fond au milieu de mes viscères;<br />
<sup>15 </sup>
 mon palais est sec comme un tesson, et ma langue collée à ma mâchoire. Tu me couches dans la poussière de la mort.<br />
<sup>16 </sup>
 Des chiens nombreux me cernent, une bande de vauriens m\'entoure; comme pour déchiqueter mes mains et mes pieds.<br />
<sup>17 </sup>
 Je peux compter tous mes os, les gens me voient, ils me regardent;<br />
<sup>18 </sup>
 ils partagent entre eux mes habits et tirent au sort mon vêtement.<br />
<sup>19 </sup>
 Mais toi, Yahvé, ne sois pas loin, ô ma force, vite à mon aide;<br />
<sup>20 </sup>
 délivre de l\'épée mon âme, de la patte du chien, mon unique;<br />
<sup>21 </sup>
 sauve-moi de la gueule du lion, de la corne du taureau, ma pauvre âme.<br />
<sup>22 </sup>
 J\'annoncerai ton nom à mes frères, en pleine assemblée je te louerai<br />
<sup>23 </sup>
 «Vous qui craignez Yahvé, louez-le, toute la race de Jacob, glorifiez-le, redoutez-le, toute la race d\'Israël.»<br />
<sup>24 </sup>
 Car il n\'a point méprisé, ni dédaigné la pauvreté du pauvre, ni caché de lui sa face, mais, invoqué par lui, il écouta.<br />
<sup>25 </sup>
 De toi vient ma louange dans la grande assemblée, j\'accomplirai mes vœux devant ceux qui le craignent.<br />
<sup>26 </sup>
 Les pauvres mangeront et seront rassasiés. Ils loueront Yahvé, ceux qui le cherchent »que vive votre cœur à jamais!»<br />
<sup>27 </sup>
 Tous les lointains de la terre se souviendront et reviendront vers Yahvé; toutes les familles des nations se prosterneront devant lui.<br />
<sup>28 </sup>
 A Yahvé la royauté, au maître des nations!<br />
<sup>29 </sup>
 Oui, devant lui seul se prosterneront tous les puissants de la terre, devant lui se courberont tous ceux qui descendent à la poussière et pour celui qui ne vit plus,<br />
<sup>30 </sup>
 sa lignée le servira, elle annoncera le Seigneur aux âges<br />
<sup>31 </sup>
 à venir, elle racontera aux peuples à naître sa justice il l\'a faite!

<accronym title=\'Psaumes\'>Ps</accronym> 22 (<i>Bible de Jérusalem (1973)</i>)



</quote>',
    1 => '<quote><strong>22</strong><sup>1 </sup>
							
									
									Du maître de chant. Sur »la biche de l\'aurore.» Psaume. De David. Mon Dieu, mon Dieu, pourquoi m\'as-tu abandonné? Loin de me sauver, les paroles que je rugis!
									
						
	
					
							
							<br /><sup>2 </sup>
							
									
									 Mon Dieu, le jour j\'appelle et tu ne réponds pas, la nuit, point de silence pour moi.
									
						
	
					
							
							<br /><sup>3 </sup>
							
									
									 Et toi, le Saint, qui habites les louanges d\'Israël!
									
						
	
					
							
							<br /><sup>4 </sup>
							
									
									 en toi nos pères avaient confiance, confiance, et tu les délivrais,
									
						
	
					
							
							<br /><sup>5 </sup>
							
									
									 vers toi ils criaient, et ils échappaient, en toi leur confiance, et ils n\'avaient pas honte.
									
						
	
					
							
							<br /><sup>6 </sup>
							
									
									 Et moi, ver et non pas homme, risée des gens, mépris du peuple,
									
						
	
					
							
							<br /><sup>7 </sup>
							
									
									 tous ceux qui me voient me bafouent, leur bouche ricane, ils hochent la tête
									
						
	
					
							
							<br /><sup>8 </sup>
							
									
									 «Il s\'est remis à Yahvé, qu\'il le délivre! qu\'il le libère, puisqu\'il est son ami!»
									
						
	
					
							
							<br /><sup>9 </sup>
							
									
									 C\'est toi qui m\'as tiré du ventre, ma confiance près des mamelles de ma mère;
									
						
	
					
							
							<br /><sup>10 </sup>
							
									
									 sur toi je fus jeté au sortir des entrailles; dès le ventre de ma mère, mon Dieu c\'est toi.
									
						
	
					
							
							<br /><sup>11 </sup>
							
									
									 Ne sois pas loin : proche est l\'angoisse, point de secours!
									
						
	
					
							
							<br /><sup>12 </sup>
							
									
									 Des taureaux nombreux me cernent, de fortes bêtes de Bashân m\'encerclent;
									
						
	
					
							
							<br /><sup>13 </sup>
							
									
									 contre moi bâille leur gueule, lions lacérant et rugissant.
									
						
	
					
							
							<br /><sup>14 </sup>
							
									
									 Comme l\'eau je m\'écoule et tous mes os se disloquent; mon cœur est pareil à la cire, il fond au milieu de mes viscères;
									
						
	
					
							
							<br /><sup>15 </sup>
							
									
									 mon palais est sec comme un tesson, et ma langue collée à ma mâchoire. Tu me couches dans la poussière de la mort.
									
						
	
					
							
							<br /><sup>16 </sup>
							
									
									 Des chiens nombreux me cernent, une bande de vauriens m\'entoure; comme pour déchiqueter mes mains et mes pieds.
									
						
	
					
							
							<br /><sup>17 </sup>
							
									
									 Je peux compter tous mes os, les gens me voient, ils me regardent;
									
						
	
					
							
							<br /><sup>18 </sup>
							
									
									 ils partagent entre eux mes habits et tirent au sort mon vêtement.
									
						
	
					
							
							<br /><sup>19 </sup>
							
									
									 Mais toi, Yahvé, ne sois pas loin, ô ma force, vite à mon aide;
									
						
	
					
							
							<br /><sup>20 </sup>
							
									
									 délivre de l\'épée mon âme, de la patte du chien, mon unique;
									
						
	
					
							
							<br /><sup>21 </sup>
							
									
									 sauve-moi de la gueule du lion, de la corne du taureau, ma pauvre âme.
									
						
	
					
							
							<br /><sup>22 </sup>
							
									
									 J\'annoncerai ton nom à mes frères, en pleine assemblée je te louerai
									
						
	
					
							
							<br /><sup>23 </sup>
							
									
									 «Vous qui craignez Yahvé, louez-le, toute la race de Jacob, glorifiez-le, redoutez-le, toute la race d\'Israël.»
									
						
	
					
							
							<br /><sup>24 </sup>
							
									
									 Car il n\'a point méprisé, ni dédaigné la pauvreté du pauvre, ni caché de lui sa face, mais, invoqué par lui, il écouta.
									
						
	
					
							
							<br /><sup>25 </sup>
							
									
									 De toi vient ma louange dans la grande assemblée, j\'accomplirai mes vœux devant ceux qui le craignent.
									
						
	
					
							
							<br /><sup>26 </sup>
							
									
									 Les pauvres mangeront et seront rassasiés. Ils loueront Yahvé, ceux qui le cherchent »que vive votre cœur à jamais!»
									
						
	
					
							
							<br /><sup>27 </sup>
							
									
									 Tous les lointains de la terre se souviendront et reviendront vers Yahvé; toutes les familles des nations se prosterneront devant lui.
									
						
	
					
							
							<br /><sup>28 </sup>
							
									
									 A Yahvé la royauté, au maître des nations!
									
						
	
					
							
							<br /><sup>29 </sup>
							
									
									 Oui, devant lui seul se prosterneront tous les puissants de la terre, devant lui se courberont tous ceux qui descendent à la poussière et pour celui qui ne vit plus,
									
						
	
					
							
							<br /><sup>30 </sup>
							
									
									 sa lignée le servira, elle annoncera le Seigneur aux âges
									
						
	
					
							
							<br /><sup>31 </sup>
							
									
									 à venir, elle racontera aux peuples à naître sa justice il l\'a faite!
									
						<p><accronym title=\'Psaumes\'>Ps</accronym> 22 (<i>Bible de Jérusalem (1973)</i>)</p></quote>',
  ),
);
		return $essais;
	}






?>
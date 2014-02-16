<?php
require_once('lanceur_spip.php');
include_spip('public/composer');
		
class Test_balise_notes extends SpipTest{

	function viderNotes(){
		// attention a cette globale qui pourrait changer dans le temps
		$GLOBALS["marqueur_notes"] = 0;
		$GLOBALS["notes_vues"] = array();
	}
	
	function testNoteSeule(){
		$texte = propre("[[Note en bas de page]]");
		// id de la note en pied de page
		$this->assertPattern('/#nb1/', $texte);
		// classe sur le lien vers le pied
		$this->assertPattern('/spip_note/', $texte);
		// id du lien pour remonter ici
		$this->assertPattern('/nh1/', $texte);
		
		// calculer les notes
		$note = calculer_notes();
		$this->assertPattern('/nb1/', $note);
		$this->assertPattern('/#nh1/', $note);
		$this->assertPattern('/Note en bas de page/', $note);
		
		// vider toutes les infos de notes
		$this->viderNotes();
	}
	
	function testNoteSeuleEtTexte(){
		$texte = propre("Texte avant [[Note en bas de page]] texte apres");
		$this->assertPattern('/#nb1/', $texte);
		$this->assertPattern('/nh1/', $texte);
		$this->assertPattern('/spip_note/', $texte);
		$this->assertPattern('/Texte avant/', $texte);
		$this->assertPattern('/texte apres/', $texte);
		$note = calculer_notes();
		$this->assertPattern('/nb1/', $note);
		$this->assertPattern('/#nh1/', $note);
		$this->assertPattern('/Note en bas de page/', $note);
		$this->viderNotes();
	}	
	function testNoteDoubleEtTexte(){
		$texte =  propre("Texte avant [[Note en bas de page]] texte apres [[Seconde note en bas de page]]");
		$this->assertPattern('/#nb1/', $texte);
		$this->assertPattern('/#nb2/', $texte);
		$this->assertPattern('/texte apres/', $texte);
		$note = calculer_notes();
		$this->assertPattern('/Note en bas de page/', $note);
		$this->assertPattern('/Seconde note en bas de page/', $note);
		$this->viderNotes();
	}	
	// en ne vidant pas les notes
	// les identifiant des renvois changent
	function testNoteDoubleDeuxFoisEtDeuxCalculs(){	
		$texte =  propre("Texte avant [[Note en bas de page]] texte apres [[Seconde note en bas de page]]");
		$note = calculer_notes();
		$texte2 =  propre("Autre avant [[Pinguin en bas de page]] autre apres [[Marmotte en bas de page]]");
		$note2 = calculer_notes();
		
		$this->assertPattern('/#nb1/', $texte);
		$this->assertPattern('/#nb2/', $texte);
		$this->assertPattern('/#nb1-1/', $texte2);
		$this->assertPattern('/#nb1-2/', $texte2);		
		
		$this->assertPattern('/Note en bas de page/', $note);
		$this->assertPattern('/Seconde note en bas de page/', $note);
		$this->assertPattern('/Pinguin en bas de page/', $note2);
		$this->assertPattern('/Marmotte en bas de page/', $note2);
		
		$this->viderNotes();		
	}
	// en ne vidant pas les notes
	// les identifiant des renvois changent
	function testNoteDoubleDeuxFoisEtUnCalcul(){	
		$texte =  propre("Texte avant [[Note en bas de page]] texte apres [[Seconde note en bas de page]]");
		$texte2 =  propre("Autre avant [[Pinguin en bas de page]] autre apres [[Marmotte en bas de page]]");
		$note = calculer_notes();
		
		$this->assertPattern('/#nb1/', $texte);
		$this->assertPattern('/#nb2/', $texte);
		$this->assertPattern('/#nb3/', $texte2);
		$this->assertPattern('/#nb4/', $texte2);		
		
		$this->assertPattern('/Note en bas de page/', $note);
		$this->assertPattern('/Seconde note en bas de page/', $note);
		$this->assertPattern('/Pinguin en bas de page/', $note);
		$this->assertPattern('/Marmotte en bas de page/', $note);
		
		$this->viderNotes();		
	}
	
	function testNoteDoubleCoupeParModele(){	
		$texte =  propre("Texte avant [[Note en bas de page]] <img1> [[Seconde note en bas de page]]");
		$this->assertPattern('/#nb1/', $texte);
		$this->assertPattern('/#nb2/', $texte);
		
		$note = calculer_notes();
		$this->assertPattern('/Note en bas de page/', $note);
		$this->assertPattern('/Seconde note en bas de page/', $note);
		$this->viderNotes();						
	}
	
	/**
	 * Ce bloc teste le bug introduit en
	 * http://trac.rezo.net/trac/spip/changeset/8847
	 * et corrige en
	 * http://trac.rezo.net/trac/spip/changeset/8872
	 */
	function testNoteNonSupprimeeSiInclureInlineOuBaliseModele(){
		$code = "
			[(#EVAL{\"chr(91).chr(91).'Ma note'.chr(93).chr(93)\"}|propre|?)]
			[(#INCLURE{fond=#DOSSIER_SQUELETTE/inclus_rien})]
			[(#NOTES|match{Ma note}|?{'OK','Une note mangee par #INCLURE'})]
		";
		$this->assertOkCode($code);
		$this->viderNotes();
	}
}



?>

<?php
class SoapClientLocal extends SoapClient{
	
	public function login($nic,$pass,$lang,$bool) {
		return(true);
	}
	
	public function logout($session) {
		return(true);
	}
	
	public function mailingListFullInfo($session,$domain,$liste) {
		$result = array(  'domain' => 'mondomaine.tld',
								 'ml' => 'listetest',
								 'owner' => 'user2@mondomaine.tld',
								 'nbSubscribers' => 2,
								 'message_moderation' => '',
								 'users_post_only' => 1,
								 'subscription_moderation' => '',
								 'replyto' => 'lastuser',
								 'lang' => 'fr',
								 'moderators' => array('user1@mondomaine.tld','user2@mondomaine.tld'),
								 'subscribers' => array('user1@mondomaine.tld','user3@free.fr')							
							) ;
		return($result);
	}
	
	public function mailingListList($session,$domain) {
		$result = array(0 => new liste('liste1',3) ,
							 1 => new liste('liste2',4) ,
							 4 => new liste('liste6',4) ,
							 2 => new liste('liste3',4) ,
							 3 => new liste('liste4',4) ,
							 5 => new liste('liste7',4) ,
							 6 => new liste('liste8',4) );
		return($result);
	}
	
	public function mailingListSubscriberList($session,$domain,$liste) {
		switch($liste) {
			case "liste1" :
				$result = array(0 => 'user3@free.fr' ,
									 1 => 'user4@free.fr' ,
									 2 => 'user1@mondomaine.tld' );
				break ;
			case "liste2" :
				$result = array(0 => 'user2@mondomaine.tld' ,
									 1 => 'user1@mondomaine.tld' );
				break ;
			case "liste3" :
			case "liste4" :
			case "liste5" :
			case "liste6" :
			case "liste7" :
			case "liste8" :
				$result = array(0 => 'user2@mondomaine.tld' ,
									 1 => 'user1@mondomaine.tld' ,
									 2 => 'user4@mondomaine.tld' ,
									 3 => 'user3@mondomaine.tld' );
				break ;
			default :
				$result = array();
				break ;
		}
		return($result);
	}
	
	public function mailingListSubscriberDel($session,$domain,$liste,$email) {
	}
	public function mailingListSubscriberAdd($session,$domain,$liste,$email) {
	}
	public function mailingListSubscriberListByEmail($session,$domain,$liste,$email) {
	}
	
}


class liste
{
	var $ml ;
	var $nbSubscribers ;
	function liste($nom,$nb) {
		$this->ml = $nom ;
		$this->nbSubscribers = $nb ;
	}
}

?>
<?php
/*Import Friends from Lovento
 * You can Posts Messages using Xuqa system
 */
$_pluginInfo=array(
	'name'=>'Xuqa',
	'version'=>'1.0.5',
	'description'=>"Get the contacts from a Xuqa account",
	'base_version'=>'1.8.0',
	'type'=>'social',
	'check_url'=>'http://xuqa.com/login.php?dest=%2Findex.php&query_str=',
	'requirement'=>'email',
	'allowed_domains'=>false,
	);
/**
 * Xuqa Plugin
 * 
 * Import Friends from Xuqa
 * You can Write Private Messages using Xuqa system
 * 
 * @author OpenInviter
 * @version 1.0.3
 */
class xuqa extends openinviter_base
	{
	private $login_ok=false;
	public $showContacts=true;
	public $internalError=false;
	protected $timeout=30;
	
	public $debug_array=array(
				'initial_get'=>'email_1',
				'login_post'=>'logout',
				'url_all_friends'=>'shadetabs',
				'get_friends'=>'name',
				'url_send_message'=>'book_id',
				'send_message'=>'book_id'
				);
	
	/**
	 * Login function
	 * 
	 * Makes all the necessary requests to authenticate
	 * the current user to the server.
	 * 
	 * @param string $user The current user.
	 * @param string $pass The password for the current user.
	 * @return bool TRUE if the current user was authenticated successfully, FALSE otherwise.
	 */
	public function login($user,$pass)
		{
		$this->resetDebugger();
		$this->service='xuqa';
		$this->service_user=$user;
		$this->service_password=$pass;	
		if (!$this->init()) return false;
		
		$res=$this->get("http://xuqa.com/login.php?dest=%2Findex.php&query_str=",true);
		if ($this->checkResponse("initial_get",$res))
			$this->updateDebugBuffer('initial_get',"http://www.xuqa.com/en/",'GET');
		else
			{
			$this->updateDebugBuffer('initial_get',"http://www.xuqa.com/en/",'GET',false);
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
			
		$form_action="http://xuqa.com/redirect-login.php?cmd=submit&";
		$post_elements=array('email_1'=>$user,'password'=>$pass,'loginform_Submit'=>'submit');
		$res=$this->post($form_action,$post_elements,true);
		if ($this->checkResponse("login_post",$res))
			$this->updateDebugBuffer('login_post',"{$form_action}",'POST',true,$post_elements);
		else
			{
			$this->updateDebugBuffer('login_post',"{$form_action}",'POST',false,$post_elements);
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
		$url_friends='http://xuqa.com/friends.php';
		$res=$this->get($url_friends,true);
		if ($this->checkResponse("url_all_friends",$res))
			$this->updateDebugBuffer('url_all_friends',$url_friends,'GET');
		else
			{
			$this->updateDebugBuffer('url_all_friends',$url_friends,'GET',false);
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
		
		$url_all_friends='http://xuqa.com/ajax/friends.php?search=all_friend&tabs=shadetabs=0&id='.$this->getElementString($res,'shadetabs=0&id=','"');
		$this->login_ok=$url_all_friends;
		return true;
		}

	/**
	 * Get the current user's contacts
	 * 
	 * Makes all the necesarry requests to import
	 * the current user's contacts
	 * 
	 * @return mixed The array if contacts if importing was successful, FALSE otherwise.
	 */	
	public function getMyContacts()
		{
		if (!$this->login_ok)
			{
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
		else $url=$this->login_ok;
		$res=$this->get($url);
		if ($this->checkResponse("get_friends",$res))
			$this->updateDebugBuffer('get_friends',$url,'GET');
		else
			{
			$this->updateDebugBuffer('get_friends',$url,'GET',false);
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
		
		$contacts=array();
		$doc=new DOMDocument();libxml_use_internal_errors(true);if (!empty($res)) $doc->loadHTML($res);libxml_use_internal_errors(false);
		$xpath=new DOMXPath($doc);$query="//span[@id='name']";$data=$xpath->query($query);
		foreach($data as $node)
			{
			$name=$node->nodeValue;
			$href=$node->parentNode->getAttribute('href');
			if (!empty($href)) $contacts[$href]=(!empty($name)?$name:false);	
			}
		return $contacts;
		}

	/**
	 * Send message to contacts
	 * 
	 * Sends a message to the contacts using
	 * the service's inernal messaging system
	 * 
	 * @param string $cookie_file The location of the cookies file for the current session
	 * @param string $message The message being sent to your contacts
	 * @param array $contacts An array of the contacts that will receive the message
	 * @return mixed FALSE on failure.
	 */
	public function sendMessage($session_id,$message,$contacts)
		{
		$countMessages=0;
		foreach($contacts as $href=>$name)
			{	
			$countMessages++;		
			$url_send_message=str_replace('1','scrapbook.php?id=1',$href);
			$res=$this->get($url_send_message,true);
			if ($this->checkResponse("url_send_message",$res))
				$this->updateDebugBuffer('url_send_message',$url_send_message,'GET');
			else
				{
				$this->updateDebugBuffer('url_send_message',$url_send_message,'GET',false);
				$this->debugRequest();
				$this->stopPlugin();
				return false;
				}
			
			$form_action='http://xuqa.com/scrapbook.php'.$this->getElementString($res,'name="scrap_post" method="post" action="','"');
			$book_id=$this->getElementDOM($res,"//input[@name='book_id']",'value');
			$post_elements=array('cmd'=>'post',
								 'book_id'=>$book_id[0],
								 'comment'=>$message['body'],
								);
			$res=$this->post($form_action,$post_elements,true);
			if ($this->checkResponse("send_message",$res))
				$this->updateDebugBuffer('send_message',"{$form_action}",'POST',true,$post_elements);
			else
				{
				$this->updateDebugBuffer('send_message',"{$form_action}",'POST',false,$post_elements);
				$this->debugRequest();
				$this->stopPlugin();
				return false;
				}
			sleep($this->messageDelay);
			if ($countMessages>$this->maxMessages) {$this->debugRequest();$this->resetDebugger();$this->stopPlugin();break;}
			}
	
		}

	/**
	 * Terminate session
	 * 
	 * Terminates the current user's session,
	 * debugs the request and reset's the internal 
	 * debudder.
	 * 
	 * @return bool TRUE if the session was terminated successfully, FALSE otherwise.
	 */	
	public function logout()
		{
		if (!$this->checkSession()) return false;
		$res=$this->get("http://xuqa.com/login.php");
		$this->debugRequest();
		$this->resetDebugger();
		$this->stopPlugin();
		return true;	
		}
	}	

?>
<?php

    include_spip('inc/library/OAuthRequestVerifier');

    function OauthVerifier($token_type = 'false') {
    
        $return = array();
    
    #    $return['request'] = $_REQUEST['oauth_consumer_key'];

    
        //Si la requete est au format Oauth
        if (OAuthRequestVerifier::requestIsSigned()) {
            try {
                $req = new OAuthRequestVerifier();
                $user_id = $req->verify($token_type);

                // If we have an user_id, then login as that user (for this request)
                if ($user_id) {     
                    $return['result'] = true;
                    $return['message'] = $user_id;
                }
            }
            catch (OAuthException $e) {
                // The request was signed, but failed verification      
                $return['result'] = false;
                $return['apache_error'] = "401	Authorization Required";
                $return['message'] = $e->getMessage();
                return $return;
            }
        //Sinon la requet n'est pas oauth
        } else {
            $return['result'] = false;
            $return['apache_error'] = "403	Forbidden";
            $return['message'] = "La requete n'est pas une requete Oauth";
        }
                
        return $return;
    }

?>

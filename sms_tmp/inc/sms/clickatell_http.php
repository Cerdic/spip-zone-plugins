<?php
// ATTENTION ! Genere avec makeSMSinc.sh et patch_SMS.php, NE PAS EDITER !
/**
 * @package Net_SMS
 */

/**
 * HTTP_Request class.
 */
include_spip('inc/c_http_request');

/**
 * Net_SMS_clickatell_http Class implements the HTTP API for accessing the
 * Clickatell (www.clickatell.com) SMS gateway.
 *
 * Copyright 2003-2006 Marko Djukic <marko@oblo.com>
 *
 * See the enclosed file COPYING for license information (LGPL). If you did
 * not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * $Horde: framework/Net_SMS/SMS/clickatell_http.php,v 1.25 2006/08/28 09:06:22 jan Exp $
 *
 * @author Marko Djukic <marko@oblo.com>
 * @package Net_SMS
 */
class Net_SMS_clickatell_http extends Net_SMS {

    var $_session_id = null;
    var $_base_url = 'http://api.clickatell.com/http/';

    /**
     * An array of capabilities, so that the driver can report which operations
     * it supports and which it doesn't. Possible values are:<pre>
     *   auth        - The gateway require authentication before sending;
     *   batch       - Batch sending is supported;
     *   multi       - Sending of messages to multiple recipients is supported;
     *   receive     - Whether this driver is capable of receiving SMS;
     *   credit      - Is use of the gateway based on credits;
     *   addressbook - Are gateway addressbooks supported;
     *   lists       - Gateway support for distribution lists.
     * </pre>
     *
     * @var array
     */
    var $capabilities = array('auth'        => true,
                              'batch'       => 100,
                              'multi'       => true,
                              'receive'     => false,
                              'credit'      => true,
                              'addressbook' => false,
                              'lists'       => false);

    /**
     * Authenticate at the gateway and set a session id if successful. Caching
     * is used to minimise the http calls for subsequent messages.
     *
     * @access private
     *
     * @return mixed  True on success or PEAR Error on failure.
     */
    function _authenticate()
    {
        /* We have already authenticated so return true. */
        if (!empty($this->_session_id)) {
            return true;
        }

        /* Set up the http authentication url. */
        $url = sprintf('auth?user=%s&password=%s&api_id=%s',
                       urlencode($this->_params['user']),
                       urlencode($this->_params['password']),
                       $this->_params['api_id']);

        /* Do the HTTP authentication and get the response. */
        $response = Net_SMS_clickatell_http::_callURL($url);
        if (is_a($response, 'c_Error')) {
            return c_PEAR::raiseError(sprintf(_L("Authentication failed. %s"), $response->getMessage()));
        }

        /* Split up the response. */
        $response = explode(':', $response);
        if ($response[0] == 'OK') {
            $this->_session_id = trim($response[1]);
            return true;
        } else {
            return $this->getError($response[1], _L("Authentication failed. %s"));
        }
    }

    /**
     * This function does the actual sending of the message.
     *
     * @access private
     *
     * @param array $message  The array containing the message and its send
     *                        parameters.
     * @param array $to       The recipients.
     *
     * @return array  An array with the success status and additional
     *                information.
     */
    function _send($message, $to)
    {
        /* Set up the http sending url. */
        $url = sprintf('sendmsg?session_id=%s&text=%s',
                       $this->_session_id,
                       urlencode($message['text']));

        $req_feat = 0;
        if (!empty($message['send_params']['from'])) {
            /* If source from is set, require it for transit gateways and append
               to url. */
            $req_feat =+ 16;
            $url .= '&from=' . urlencode($message['send_params']['from']);
        }
        if (!empty($message['send_params']['msg_type']) &&
            $message['send_params']['msg_type'] == 'SMS_FLASH') {
            /* If message type is flash, require it for transit gateways. */
            $req_feat =+ 512;
            $url .= '&msg_type=' . $message['send_params']['msg_type'];
        }
        if (!empty($req_feat)) {
            /* If features have been required, add to url. */
            $url .= '&req_feat=' . $req_feat;
        }

        /* Append the recipients of this message and call the url. */
        foreach ($to as $key => $val) {
            if (preg_match('/^.*?<?\+?(\d{7,})(>|$)/', $val, $matches)) {
                $to[$key] = $matches[1];
            } else {
                /* FIXME: Silently drop bad recipients. This should be logged
                 * and/or reported. */
                unset($to[$key]);
            }
        }
        $to = implode(',', $to);
        $url .= '&to=' . $to;
        $response = trim($this->_callURL($url));

        /* Ugly parsing of the response, but that's how it comes back. */
        $lines = explode("\n", $response);
        $response = array();

        if (count($lines) > 1) {
            foreach ($lines as $line) {
                $parts = explode('To:', $line);
                $recipient = trim($parts[1]);
                $outcome = explode(':', $parts[0]);
                $response[$recipient] = array(($outcome[0] == 'ID' ? 1 : 0), $outcome[1]);
            }
        } else {
            /* Single recipient. */
            $outcome = explode(':', $lines[0]);
            $response[$to] = array(($outcome[0] == 'ID' ? 1 : 0), $outcome[1]);
        }

        return $response;
    }

    /**
     * Returns the current credit balance on the gateway.
     *
     * @access private
     *
     * @return integer  The credit balance available on the gateway.
     */
    function _getBalance()
    {
        /* Set up the url and call it. */
        $url = sprintf('getbalance?session_id=%s',
                       $this->_session_id);
        $response = trim($this->_callURL($url));

        /* Try splitting up the response. */
        $lines = explode('=', $response);

        /* Split up the response. */
        $response = explode(':', $response);
        if ($response[0] == 'Credit') {
            return trim($response[1]);
        } else {
            return $this->getError($response[1], _L("Could not check balance. %s"));
        }
    }

    /**
     * Identifies this gateway driver and returns a brief description.
     *
     * @return array  Array of driver info.
     */
    function getInfo()
    {
        return array(
            'name' => _L("Clickatell via HTTP"),
            'desc' => _L("This driver allows sending of messages through the Clickatell (http://clickatell.com) gateway, using the HTTP API"),
        );
    }

    /**
     * Returns the required parameters for this gateway driver.
     *
     * @return array  Array of required parameters.
     */
    function getParams()
    {
        return array(
            'user' => array('label' => _L("Username"), 'type' => 'text'),
            'password' => array('label' => _L("Password"), 'type' => 'text'),
            'api_id' => array('label' => _L("API ID"), 'type' => 'text'),
        );
    }

    /**
     * Returns the parameters that can be set as default for sending messages
     * using this gateway driver and displayed when sending messages.
     *
     * @return array  Array of parameters that can be set as default.
     * @todo  Set up batch fields/params, would be nice to have ringtone/logo
     *        support too, queue choice, unicode choice.
     */
    function getDefaultSendParams()
    {
        $params = array();
        $params['from'] = array(
            'label' => _L("Source address"),
            'type' => 'text');

        $params['deliv_time'] = array(
            'label' => _L("Delivery time"),
            'type' => 'enum',
            'params' => array(array('now' => _L("immediate"), 'user' => _L("user select"))));

        $types = array('SMS_TEXT' => _L("Standard"), 'SMS_FLASH' => _L("Flash"));
        $params['msg_type'] = array(
            'label' => _L("Message type"),
            'type' => 'keyval_multienum',
            'params' => array($types));

        return $params;
    }

    /**
     * Returns the parameters for sending messages using this gateway driver,
     * displayed when sending messages. These are filtered out using the
     * default values set for the gateway.
     *
     * @return array  Array of required parameters.
     * @todo  Would be nice to use a time/date setup rather than minutes from
     *        now for the delivery time. Upload field for ringtones/logos?
     */
    function getSendParams($params)
    {
        if (empty($params['from'])) {
            $params['from'] = array(
                'label' => _L("Source address"),
                'type' => 'text');
        }

        if ($params['deliv_time'] == 'user') {
            $params['deliv_time'] = array(
                'label' => _L("Delivery time"),
                'type' => 'int',
                'desc' => _L("Value in minutes from now."));
        }

        if (count($params['msg_type']) > 1) {
            $params['msg_type'] = array(
                'label' => _L("Message type"),
                'type' => 'enum',
                'params' => array($params['msg_type']));
        } else {
            $params['msg_type'] = $params['msg_type'][0];
        }

        return $params;
    }

    /**
     * Returns a string representation of an error code.
     *
     * @param integer $error  The error code to look up.
     * @param string $text    An existing error text to use to raise a
     *                        PEAR Error.
     *
     * @return mixed  A textual message corresponding to the error code or a
     *                PEAR Error if passed an existing error text.
     *
     * @todo  Check which of these are actually required and trim down the
     *        list.
     */
    function getError($error, $error_text = '')
    {
        /* Make sure we get only the number at the start of an error. */
        list($error) = explode(',', $error);
        $error = trim($error);

        /* An array of error codes returned by the gateway. */
        $errors = array('001' => _L("Authentication failed"),
                        '002' => _L("Unknown username or password."),
                        '003' => _L("Session ID expired."),
                        '004' => _L("Account frozen."),
                        '005' => _L("Missing session ID."),
                        '007' => _L("IP lockdown violation."),
                        '101' => _L("Invalid or missing parameters."),
                        '102' => _L("Invalid UDH. (User Data Header)."),
                        '103' => _L("Unknown apimsgid (API Message ID)."),
                        '104' => _L("Unknown climsgid (Client Message ID)."),
                        '105' => _L("Invalid destination address."),
                        '106' => _L("Invalid source address."),
                        '107' => _L("Empty message."),
                        '108' => _L("Invalid or missing api_id."),
                        '109' => _L("Missing message ID."),
                        '110' => _L("Error with email message."),
                        '111' => _L("Invalid protocol."),
                        '112' => _L("Invalid msg_type."),
                        '113' => _L("Max message parts exceeded."),
                        '114' => _L("Cannot route message to specified number."),
                        '115' => _L("Message expired."),
                        '116' => _L("Invalid unicode data."),
                        '201' => _L("Invalid batch ID."),
                        '202' => _L("No batch template."),
                        '301' => _L("No credit left."),
                        '302' => _L("Max allowed credit."));

        if (empty($error_text)) {
            return $errors[$error];
        } else {
            return c_PEAR::raiseError(sprintf($error_text, $errors[$error]));
        }
    }

    /**
     * Do the http call using a url passed to the function.
     *
     * @access private
     *
     * @param string $url  The url to call.
     *
     * @return mixed  The response on success or PEAR Error on failure.
     */
    function _callURL($url)
    {
        $options['method'] = 'GET';
        $options['timeout'] = 5;
        $options['allowRedirects'] = true;

        $http = new c_HTTP_Request($this->_base_url . $url, $options);
        @$http->sendRequest();
        if ($http->getResponseCode() != 200) {
            return c_PEAR::raiseError(sprintf(_L("Could not open %s."), $url));
        }

        return $http->getResponseBody();
    }

}

<?php

require_once 'HTMLPurifier/AttrDef.php';
require_once 'HTMLPurifier/URIScheme.php';
require_once 'HTMLPurifier/URISchemeRegistry.php';
require_once 'HTMLPurifier/AttrDef/Host.php';
require_once 'HTMLPurifier/PercentEncoder.php';

HTMLPurifier_ConfigSchema::define(
    'URI', 'DefaultScheme', 'http', 'string',
    'Defines through what scheme the output will be served, in order to '.
    'select the proper object validator when no scheme information is present.'
);

HTMLPurifier_ConfigSchema::define(
    'URI', 'Host', null, 'string/null',
    'Defines the domain name of the server, so we can determine whether or '.
    'an absolute URI is from your website or not.  Not strictly necessary, '.
    'as users should be using relative URIs to reference resources on your '.
    'website.  It will, however, let you use absolute URIs to link to '.
    'subdomains of the domain you post here: i.e. example.com will allow '.
    'sub.example.com.  However, higher up domains will still be excluded: '.
    'if you set %URI.Host to sub.example.com, example.com will be blocked. '.
    'This directive has been available since 1.2.0.'
);

HTMLPurifier_ConfigSchema::Define(
    'URI', 'DisableExternal', false, 'bool',
    'Disables links to external websites.  This is a highly effective '.
    'anti-spam and anti-pagerank-leech measure, but comes at a hefty price: no'.
    'links or images outside of your domain will be allowed.  Non-linkified '.
    'URIs will still be preserved.  If you want to be able to link to '.
    'subdomains or use absolute URIs, specify %URI.Host for your website. '.
    'This directive has been available since 1.2.0.'
);

/**
 * Validates a URI as defined by RFC 3986.
 * @note Scheme-specific mechanics deferred to HTMLPurifier_URIScheme
 */
class HTMLPurifier_AttrDef_URI extends HTMLPurifier_AttrDef
{
    
    var $host;
    var $PercentEncoder;
    var $embeds;
    
    /**
     * @param $embeds Does the URI here result in an extra HTTP request?
     */
    function HTMLPurifier_AttrDef_URI($embeds = false) {
        $this->host = new HTMLPurifier_AttrDef_Host();
        $this->PercentEncoder = new HTMLPurifier_PercentEncoder();
        $this->embeds = (bool) $embeds;
    }
    
    function validate($uri, $config, &$context) {
        
        // We'll write stack-based parsers later, for now, use regexps to
        // get things working as fast as possible (irony)
        
        // parse as CDATA
        $uri = $this->parseCDATA($uri);
        
        // fix up percent-encoding
        $uri = $this->PercentEncoder->normalize($uri);
        
        // while it would be nice to use parse_url(), that's specifically
        // for HTTP and thus won't work for our generic URI parsing
        
        // according to the RFC... (but this cuts corners, i.e. non-validating)
        $r_URI = '!'.
            '(([^:/?#<>\'"]+):)?'. // 2. Scheme
            '(//([^/?#<>\'"]*))?'. // 4. Authority
            '([^?#<>\'"]*)'.       // 5. Path
            '(\?([^#<>\'"]*))?'.   // 7. Query
            '(#([^<>\'"]*))?'.     // 8. Fragment
            '!';
        
        $matches = array();
        $result = preg_match($r_URI, $uri, $matches);
        
        if (!$result) return false; // invalid URI
        
        // seperate out parts
        $scheme     = !empty($matches[1]) ? $matches[2] : null;
        $authority  = !empty($matches[3]) ? $matches[4] : null;
        $path       = $matches[5]; // always present, can be empty
        $query      = !empty($matches[6]) ? $matches[7] : null;
        $fragment   = !empty($matches[8]) ? $matches[9] : null;
        
        
        
        $registry =& HTMLPurifier_URISchemeRegistry::instance();
        if ($scheme !== null) {
            // no need to validate the scheme's fmt since we do that when we
            // retrieve the specific scheme object from the registry
            $scheme = ctype_lower($scheme) ? $scheme : strtolower($scheme);
            $scheme_obj =& $registry->getScheme($scheme, $config, $context);
            if (!$scheme_obj) return false; // invalid scheme, clean it out
        } else {
            $scheme_obj =& $registry->getScheme(
                $config->get('URI', 'DefaultScheme'), $config, $context
            );
        }
        
        
        // the URI we're processing embeds a resource in the page, but the URI
        // it references cannot be located
        if ($this->embeds && !$scheme_obj->browsable) {
            return false;
        }
        
        
        if ($authority !== null) {
            
            // remove URI if it's absolute and we disallow externals
            unset($our_host);
            if ($config->get('URI', 'DisableExternal')) {
                $our_host = $config->get('URI', 'Host');
                if ($our_host === null) return false;
            }
            
            $HEXDIG = '[A-Fa-f0-9]';
            $unreserved = 'A-Za-z0-9-._~'; // make sure you wrap with []
            $sub_delims = '!$&\'()'; // needs []
            $pct_encoded = "%$HEXDIG$HEXDIG";
            $r_userinfo = "(?:[$unreserved$sub_delims:]|$pct_encoded)*";
            $r_authority = "/^(($r_userinfo)@)?(\[[^\]]+\]|[^:]*)(:(\d*))?/";
            $matches = array();
            preg_match($r_authority, $authority, $matches);
            // overloads regexp!
            $userinfo   = !empty($matches[1]) ? $matches[2] : null;
            $host       = !empty($matches[3]) ? $matches[3] : null;
            $port       = !empty($matches[4]) ? $matches[5] : null;
            
            // validate port
            if ($port !== null) {
                $port = (int) $port;
                if ($port < 1 || $port > 65535) $port = null;
            }
            
            $host = $this->host->validate($host, $config, $context);
            if ($host === false) $host = null;
            
            // more lenient absolute checking
            if (isset($our_host)) {
                $host_parts = array_reverse(explode('.', $host));
                // could be cached
                $our_host_parts = array_reverse(explode('.', $our_host));
                foreach ($our_host_parts as $i => $discard) {
                    if (!isset($host_parts[$i])) return false;
                    if ($host_parts[$i] != $our_host_parts[$i]) return false;
                }
            }
            
            // userinfo and host are validated within the regexp
            
        } else {
            $port = $host = $userinfo = null;
        }
        
        
        // query and fragment are quite simple in terms of definition:
        // *( pchar / "/" / "?" ), so define their validation routines
        // when we start fixing percent encoding
        
        
        
        // path gets to be validated against a hodge-podge of rules depending
        // on the status of authority and scheme, but it's not that important,
        // esp. since it won't be applicable to everyone
        
        
        
        // okay, now we defer execution to the subobject for more processing
        // note that $fragment is omitted
        list($userinfo, $host, $port, $path, $query) = 
            $scheme_obj->validateComponents(
                $userinfo, $host, $port, $path, $query, $config, $context
            );
        
        
        // reconstruct authority
        $authority = null;
        if (!is_null($userinfo) || !is_null($host) || !is_null($port)) {
            $authority = '';
            if($userinfo !== null) $authority .= $userinfo . '@';
            $authority .= $host;
            if($port !== null) $authority .= ':' . $port;
        }
        
        // reconstruct the result
        $result = '';
        if ($scheme !== null) $result .= "$scheme:";
        if ($authority !== null) $result .= "//$authority";
        $result .= $path;
        if ($query !== null) $result .= "?$query";
        if ($fragment !== null) $result .= "#$fragment";
        
        return $result;
        
    }
    
}

?>

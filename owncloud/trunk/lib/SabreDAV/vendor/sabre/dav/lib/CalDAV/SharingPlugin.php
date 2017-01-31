<?php

namespace Sabre\CalDAV;

use Sabre\DAV;
use Sabre\DAV\Xml\Property\Href;
use Sabre\DAV\Xml\Property\LocalHref;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;

/**
 * This plugin implements support for caldav sharing.
 *
 * This spec is defined at:
 * http://svn.calendarserver.org/repository/calendarserver/CalendarServer/trunk/doc/Extensions/caldav-sharing.txt
 *
 * See:
 * Sabre\CalDAV\Backend\SharingSupport for all the documentation.
 *
 * Note: This feature is experimental, and may change in between different
 * SabreDAV versions.
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class SharingPlugin extends DAV\ServerPlugin {

    /**
     * Reference to SabreDAV server object.
     *
     * @var Sabre\DAV\Server
     */
    protected $server;

    /**
     * This method should return a list of server-features.
     *
     * This is for example 'versioning' and is added to the DAV: header
     * in an OPTIONS response.
     *
     * @return array
     */
    function getFeatures() {

        return ['calendarserver-sharing'];

    }

    /**
     * Returns a plugin name.
     *
     * Using this name other plugins will be able to access other plugins
     * using Sabre\DAV\Server::getPlugin
     *
     * @return string
     */
    function getPluginName() {

        return 'caldav-sharing';

    }

    /**
     * This initializes the plugin.
     *
     * This function is called by Sabre\DAV\Server, after
     * addPlugin is called.
     *
     * This method should set up the required event subscriptions.
     *
     * @param DAV\Server $server
     * @return void
     */
    function initialize(DAV\Server $server) {

        $this->server = $server;

        if (is_null($this->server->getPlugin('sharing'))) {
            throw new \LogicException('The generic "sharing" plugin must be loaded before the caldav sharing plugin. Call $server->addPlugin(new \Sabre\DAV\Sharing\Plugin()); before this one.');
        }

        array_push(
            $this->server->protectedProperties,
            '{' . Plugin::NS_CALENDARSERVER . '}invite',
            '{' . Plugin::NS_CALENDARSERVER . '}allowed-sharing-modes',
            '{' . Plugin::NS_CALENDARSERVER . '}shared-url'
        );

        $this->server->xml->elementMap['{' . Plugin::NS_CALENDARSERVER . '}share'] = 'Sabre\\CalDAV\\Xml\\Request\\Share';
        $this->server->xml->elementMap['{' . Plugin::NS_CALENDARSERVER . '}invite-reply'] = 'Sabre\\CalDAV\\Xml\\Request\\InviteReply';

        $this->server->on('propFind',     [$this, 'propFindEarly']);
        $this->server->on('propFind',     [$this, 'propFindLate'], 150);
        $this->server->on('propPatch',    [$this, 'propPatch'], 40);
        $this->server->on('method:POST',  [$this, 'httpPost']);

    }

    /**
     * This event is triggered when properties are requested for a certain
     * node.
     *
     * This allows us to inject any properties early.
     *
     * @param DAV\PropFind $propFind
     * @param DAV\INode $node
     * @return void
     */
    function propFindEarly(DAV\PropFind $propFind, DAV\INode $node) {

        if ($node instanceof ISharedCalendar) {

            $propFind->handle('{' . Plugin::NS_CALENDARSERVER . '}invite', function() use ($node) {

                // Fetching owner information
                $props = $this->server->getPropertiesForPath($node->getOwner(), [
                    '{http://sabredav.org/ns}email-address',
                    '{DAV:}displayname',
                ], 0);

                $ownerInfo = [
                    'href' => $node->getOwner(),
                ];

                if (isset($props[0][200])) {

                    // We're mapping the internal webdav properties to the
                    // elements caldav-sharing expects.
                    if (isset($props[0][200]['{http://sabredav.org/ns}email-address'])) {
                        $ownerInfo['href'] = 'mailto:' . $props[0][200]['{http://sabredav.org/ns}email-address'];
                    }
                    if (isset($props[0][200]['{DAV:}displayname'])) {
                        $ownerInfo['commonName'] = $props[0][200]['{DAV:}displayname'];
                    }

                }

                return new Xml\Property\Invite(
                    $node->getInvites(),
                    $ownerInfo
                );

            });

        }

    }

    /**
     * This method is triggered *after* all properties have been retrieved.
     * This allows us to inject the correct resourcetype for calendars that
     * have been shared.
     *
     * @param DAV\PropFind $propFind
     * @param DAV\INode $node
     * @return void
     */
    function propFindLate(DAV\PropFind $propFind, DAV\INode $node) {

        if ($node instanceof ISharedCalendar) {
            $shareAccess = $node->getShareAccess();
            if ($rt = $propFind->get('{DAV:}resourcetype')) {
                switch ($shareAccess) {
                    case \Sabre\DAV\Sharing\Plugin::ACCESS_SHAREDOWNER :
                        $rt->add('{' . Plugin::NS_CALENDARSERVER . '}shared-owner');
                        break;
                    case \Sabre\DAV\Sharing\Plugin::ACCESS_READ :
                    case \Sabre\DAV\Sharing\Plugin::ACCESS_READWRITE :
                        $rt->add('{' . Plugin::NS_CALENDARSERVER . '}shared');
                        break;

                }
            }
            $propFind->handle('{' . Plugin::NS_CALENDARSERVER . '}allowed-sharing-modes', function() {
                return new Xml\Property\AllowedSharingModes(true, false);
            });

        }

    }

    /**
     * This method is trigged when a user attempts to update a node's
     * properties.
     *
     * A previous draft of the sharing spec stated that it was possible to use
     * PROPPATCH to remove 'shared-owner' from the resourcetype, thus unsharing
     * the calendar.
     *
     * Even though this is no longer in the current spec, we keep this around
     * because OS X 10.7 may still make use of this feature.
     *
     * @param string $path
     * @param DAV\PropPatch $propPatch
     * @return void
     */
    function propPatch($path, DAV\PropPatch $propPatch) {

        $node = $this->server->tree->getNodeForPath($path);
        if (!$node instanceof ISharedCalendar)
            return;

        if ($node->getShareAccess() === \Sabre\DAV\Sharing\Plugin::ACCESS_SHAREDOWNER || $node->getShareAccess() === \Sabre\DAV\Sharing\Plugin::ACCESS_NOTSHARED) {

            $propPatch->handle('{DAV:}resourcetype', function($value) use ($node) {
                if ($value->is('{' . Plugin::NS_CALENDARSERVER . '}shared-owner')) return false;
                $shares = $node->getInvites();
                foreach ($shares as $share) {
                    $share->access = DAV\Sharing\Plugin::ACCESS_NOACCESS;
                }
                $node->updateInvites($shares);

                return true;

            });

        }

    }

    /**
     * We intercept this to handle POST requests on calendars.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return null|bool
     */
    function httpPost(RequestInterface $request, ResponseInterface $response) {

        $path = $request->getPath();

        // Only handling xml
        $contentType = $request->getHeader('Content-Type');
        if (strpos($contentType, 'application/xml') === false && strpos($contentType, 'text/xml') === false)
            return;

        // Making sure the node exists
        try {
            $node = $this->server->tree->getNodeForPath($path);
        } catch (DAV\Exception\NotFound $e) {
            return;
        }

        $requestBody = $request->getBodyAsString();

        // If this request handler could not deal with this POST request, it
        // will return 'null' and other plugins get a chance to handle the
        // request.
        //
        // However, we already requested the full body. This is a problem,
        // because a body can only be read once. This is why we preemptively
        // re-populated the request body with the existing data.
        $request->setBody($requestBody);

        $message = $this->server->xml->parse($requestBody, $request->getUrl(), $documentType);

        switch ($documentType) {

            // Both the DAV:share-resource and CALENDARSERVER:share requests
            // behave identically.
            case '{' . Plugin::NS_CALENDARSERVER . '}share' :

                $sharingPlugin = $this->server->getPlugin('sharing');
                $sharingPlugin->shareResource($path, $message->sharees);

                $response->setStatus(200);
                // Adding this because sending a response body may cause issues,
                // and I wanted some type of indicator the response was handled.
                $response->setHeader('X-Sabre-Status', 'everything-went-well');

                // Breaking the event chain
                return false;

            // The invite-reply document is sent when the user replies to an
            // invitation of a calendar share.
            case '{' . Plugin::NS_CALENDARSERVER . '}invite-reply' :

                // This only works on the calendar-home-root node.
                if (!$node instanceof CalendarHome) {
                    return;
                }
                $this->server->transactionType = 'post-invite-reply';

                // Getting ACL info
                $acl = $this->server->getPlugin('acl');

                // If there's no ACL support, we allow everything
                if ($acl) {
                    $acl->checkPrivileges($path, '{DAV:}write');
                }

                $url = $node->shareReply(
                    $message->href,
                    $message->status,
                    $message->calendarUri,
                    $message->inReplyTo,
                    $message->summary
                );

                $response->setStatus(200);
                // Adding this because sending a response body may cause issues,
                // and I wanted some type of indicator the response was handled.
                $response->setHeader('X-Sabre-Status', 'everything-went-well');

                if ($url) {
                    $writer = $this->server->xml->getWriter();
                    $writer->openMemory();
                    $writer->startDocument();
                    $writer->startElement('{' . Plugin::NS_CALENDARSERVER . '}shared-as');
                    $writer->write(new LocalHref($url));
                    $writer->endElement();
                    $response->setHeader('Content-Type', 'application/xml');
                    $response->setBody($writer->outputMemory());

                }

                // Breaking the event chain
                return false;

            case '{' . Plugin::NS_CALENDARSERVER . '}publish-calendar' :

                // We can only deal with IShareableCalendar objects
                if (!$node instanceof ISharedCalendar) {
                    return;
                }
                $this->server->transactionType = 'post-publish-calendar';

                // Getting ACL info
                $acl = $this->server->getPlugin('acl');

                // If there's no ACL support, we allow everything
                if ($acl) {
                    $acl->checkPrivileges($path, '{DAV:}share');
                }

                $node->setPublishStatus(true);

                // iCloud sends back the 202, so we will too.
                $response->setStatus(202);

                // Adding this because sending a response body may cause issues,
                // and I wanted some type of indicator the response was handled.
                $response->setHeader('X-Sabre-Status', 'everything-went-well');

                // Breaking the event chain
                return false;

            case '{' . Plugin::NS_CALENDARSERVER . '}unpublish-calendar' :

                // We can only deal with IShareableCalendar objects
                if (!$node instanceof ISharedCalendar) {
                    return;
                }
                $this->server->transactionType = 'post-unpublish-calendar';

                // Getting ACL info
                $acl = $this->server->getPlugin('acl');

                // If there's no ACL support, we allow everything
                if ($acl) {
                    $acl->checkPrivileges($path, '{DAV:}share');
                }

                $node->setPublishStatus(false);

                $response->setStatus(200);

                // Adding this because sending a response body may cause issues,
                // and I wanted some type of indicator the response was handled.
                $response->setHeader('X-Sabre-Status', 'everything-went-well');

                // Breaking the event chain
                return false;

        }



    }

    /**
     * Returns a bunch of meta-data about the plugin.
     *
     * Providing this information is optional, and is mainly displayed by the
     * Browser plugin.
     *
     * The description key in the returned array may contain html and will not
     * be sanitized.
     *
     * @return array
     */
    function getPluginInfo() {

        return [
            'name'        => $this->getPluginName(),
            'description' => 'Adds support for caldav-sharing.',
            'link'        => 'http://sabre.io/dav/caldav-sharing/',
        ];

    }
}

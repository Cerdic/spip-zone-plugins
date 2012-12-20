<?php

class Mandrill_Senders {
    public function __construct(Mandrill $master) {
        $this->master = $master;
    }

    /**
     * Return the senders that have tried to use this account.
     * @return array an array of sender data, one for each sending addresses used by the account
     *     - return[] struct the information on each sending address in the account
     *         - address string the sender's email address
     *         - created_at string the date and time that the sender was first seen by Mandrill as a UTC date string in YYYY-MM-DD HH:MM:SS format
     *         - sent integer the total number of messages sent by this sender
     *         - hard_bounces integer the total number of hard bounces by messages by this sender
     *         - soft_bounces integer the total number of soft bounces by messages by this sender
     *         - rejects integer the total number of rejected messages by this sender
     *         - complaints integer the total number of spam complaints received for messages by this sender
     *         - unsubs integer the total number of unsubscribe requests received for messages by this sender
     *         - opens integer the total number of times messages by this sender have been opened
     *         - clicks integer the total number of times tracked URLs in messages by this sender have been clicked
     */
    public function getList() {
        $_params = array();
        return $this->master->call('senders/list', $_params);
    }

    /**
     * Returns the sender domains that have been added to this account.
     * @return array an array of sender domain data, one for each sending domain used by the account
     *     - return[] struct the information on each sending domain for the account
     *         - domain string the sender domain name
     *         - created_at string the date and time that the sending domain was first seen as a UTC string in YYYY-MM-DD HH:MM:SS format
     */
    public function domains() {
        $_params = array();
        return $this->master->call('senders/domains', $_params);
    }

    /**
     * Return more detailed information about a single sender, including aggregates of recent stats
     * @param string $address the email address of the sender
     * @return struct the detailed information on the sender
     *     - address string the sender's email address
     *     - created_at string the date and time that the sender was first seen by Mandrill as a UTC date string in YYYY-MM-DD HH:MM:SS format
     *     - sent integer the total number of messages sent by this sender
     *     - hard_bounces integer the total number of hard bounces by messages by this sender
     *     - soft_bounces integer the total number of soft bounces by messages by this sender
     *     - rejects integer the total number of rejected messages by this sender
     *     - complaints integer the total number of spam complaints received for messages by this sender
     *     - unsubs integer the total number of unsubscribe requests received for messages by this sender
     *     - opens integer the total number of times messages by this sender have been opened
     *     - clicks integer the total number of times tracked URLs in messages by this sender have been clicked
     *     - stats struct an aggregate summary of the sender's sending stats
     *         - today struct stats for this sender so far today
     *             - sent integer the number of emails sent for this sender so far today
     *             - hard_bounces integer the number of emails hard bounced for this sender so far today
     *             - soft_bounces integer the number of emails soft bounced for this sender so far today
     *             - rejects integer the number of emails rejected for sending this sender so far today
     *             - complaints integer the number of spam complaints for this sender so far today
     *             - unsubs integer the number of unsubscribes for this sender so far today
     *             - opens integer the number of times emails have been opened for this sender so far today
     *             - unique_opens integer the number of unique opens for emails sent for this sender so far today
     *             - clicks integer the number of URLs that have been clicked for this sender so far today
     *             - unique_clicks integer the number of unique clicks for emails sent for this sender so far today
     *         - last_7_days struct stats for this sender in the last 7 days
     *             - sent integer the number of emails sent for this sender in the last 7 days
     *             - hard_bounces integer the number of emails hard bounced for this sender in the last 7 days
     *             - soft_bounces integer the number of emails soft bounced for this sender in the last 7 days
     *             - rejects integer the number of emails rejected for sending this sender in the last 7 days
     *             - complaints integer the number of spam complaints for this sender in the last 7 days
     *             - unsubs integer the number of unsubscribes for this sender in the last 7 days
     *             - opens integer the number of times emails have been opened for this sender in the last 7 days
     *             - unique_opens integer the number of unique opens for emails sent for this sender in the last 7 days
     *             - clicks integer the number of URLs that have been clicked for this sender in the last 7 days
     *             - unique_clicks integer the number of unique clicks for emails sent for this sender in the last 7 days
     *         - last_30_days struct stats for this sender in the last 30 days
     *             - sent integer the number of emails sent for this sender in the last 30 days
     *             - hard_bounces integer the number of emails hard bounced for this sender in the last 30 days
     *             - soft_bounces integer the number of emails soft bounced for this sender in the last 30 days
     *             - rejects integer the number of emails rejected for sending this sender in the last 30 days
     *             - complaints integer the number of spam complaints for this sender in the last 30 days
     *             - unsubs integer the number of unsubscribes for this sender in the last 30 days
     *             - opens integer the number of times emails have been opened for this sender in the last 30 days
     *             - unique_opens integer the number of unique opens for emails sent for this sender in the last 30 days
     *             - clicks integer the number of URLs that have been clicked for this sender in the last 30 days
     *             - unique_clicks integer the number of unique clicks for emails sent for this sender in the last 30 days
     *         - last_60_days struct stats for this sender in the last 60 days
     *             - sent integer the number of emails sent for this sender in the last 60 days
     *             - hard_bounces integer the number of emails hard bounced for this sender in the last 60 days
     *             - soft_bounces integer the number of emails soft bounced for this sender in the last 60 days
     *             - rejects integer the number of emails rejected for sending this sender in the last 60 days
     *             - complaints integer the number of spam complaints for this sender in the last 60 days
     *             - unsubs integer the number of unsubscribes for this sender in the last 60 days
     *             - opens integer the number of times emails have been opened for this sender in the last 60 days
     *             - unique_opens integer the number of unique opens for emails sent for this sender in the last 60 days
     *             - clicks integer the number of URLs that have been clicked for this sender in the last 60 days
     *             - unique_clicks integer the number of unique clicks for emails sent for this sender in the last 60 days
     *         - last_90_days struct stats for this sender in the last 90 days
     *             - sent integer the number of emails sent for this sender in the last 90 days
     *             - hard_bounces integer the number of emails hard bounced for this sender in the last 90 days
     *             - soft_bounces integer the number of emails soft bounced for this sender in the last 90 days
     *             - rejects integer the number of emails rejected for sending this sender in the last 90 days
     *             - complaints integer the number of spam complaints for this sender in the last 90 days
     *             - unsubs integer the number of unsubscribes for this sender in the last 90 days
     *             - opens integer the number of times emails have been opened for this sender in the last 90 days
     *             - unique_opens integer the number of unique opens for emails sent for this sender in the last 90 days
     *             - clicks integer the number of URLs that have been clicked for this sender in the last 90 days
     *             - unique_clicks integer the number of unique clicks for emails sent for this sender in the last 90 days
     */
    public function info($address) {
        $_params = array("address" => $address);
        return $this->master->call('senders/info', $_params);
    }

    /**
     * Return the recent history (hourly stats for the last 30 days) for a sender
     * @param string $address the email address of the sender
     * @return array the array of history information
     *     - return[] struct the stats for a single hour
     *         - time string the hour as a UTC date string in YYYY-MM-DD HH:MM:SS format
     *         - sent integer the number of emails that were sent during the hour
     *         - hard_bounces integer the number of emails that hard bounced during the hour
     *         - soft_bounces integer the number of emails that soft bounced during the hour
     *         - rejects integer the number of emails that were rejected during the hour
     *         - complaints integer the number of spam complaints received during the hour
     *         - opens integer the number of emails opened during the hour
     *         - unique_opens integer the number of unique opens generated by messages sent during the hour
     *         - clicks integer the number of tracked URLs clicked during the hour
     *         - unique_clicks integer the number of unique clicks generated by messages sent during the hour
     */
    public function timeSeries($address) {
        $_params = array("address" => $address);
        return $this->master->call('senders/time-series', $_params);
    }

}



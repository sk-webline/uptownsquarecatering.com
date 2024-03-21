<?php

namespace App\Gateways\VivaWallet;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UriInterface;

class Response
{

    public $event;

    public $eci;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->getEvents();

        $this->getEci();
    }

    private function getEvents() {
        $this->event = (object) array(
            0 => (object) array('reason' => 'Undefined', 'explanation' => 'Undefined', 'type' => 'System'),
            2061 => (object) array('reason' => '3DS flow incomplete', 'explanation' => 'Browser closed before authentication finished.', 'type' => 'User'),
            2062 => (object) array('reason' => '3DS validation failed', 'explanation' => 'Wrong password or two-factor auth code entered.', 'type' => 'User'),
            2108 => (object) array('reason' => 'Payments Policy Acquiring Restriction', 'explanation' => 'Payments Policy Acquiring Restriction.', 'type' => 'System'),
            10001 => (object) array('reason' => 'Refer to card issuer', 'explanation' => 'The issuing bank prevented the transaction.', 'type' => 'Issuer'),
            10003 => (object) array('reason' => 'Invalid merchant number', 'explanation' => 'Security violation (source is not correct issuer).', 'type' => 'Issuer'),
            10004 => (object) array('reason' => 'Pick up card', 'explanation' => 'The card has been designated as lost or stolen.', 'type' => 'Issuer'),
            10005 => (object) array('reason' => 'Do not honor', 'explanation' => 'The issuing bank declined the transaction without an explanation.', 'type' => 'Issuer'),
            10006 => (object) array('reason' => 'General error', 'explanation' => 'The card issuer has declined the transaction as there is a problem with the card number.', 'type' => 'Issuer'),
            10012 => (object) array('reason' => 'Invalid transaction', 'explanation' => 'The bank has declined the transaction because of an invalid format or field. This indicates the card details were incorrect.', 'type' => 'Issuer'),
            10013 => (object) array('reason' => 'Invalid amount', 'explanation' => 'The card issuer has declined the transaction because of an invalid format or field.', 'type' => 'System'),
            10014 => (object) array('reason' => 'Invalid card number', 'explanation' => 'The card issuer has declined the transaction as the credit card number is incorrectly entered or does not exist.', 'type' => 'User'),
            10015 => (object) array('reason' => 'Invalid issuer', 'explanation' => 'The card issuer doesn\'t exist.', 'type' => 'System'),
            10030 => (object) array('reason' => 'Format error', 'explanation' => 'The card issuer does not recognise the transaction details being entered. This is due to a format error.', 'type' => 'System'),
            10041 => (object) array('reason' => 'Lost card', 'explanation' => 'The card issuer has declined the transaction as the card has been reported lost.', 'type' => 'Issuer'),
            10043 => (object) array('reason' => 'Stolen card', 'explanation' => 'The card has been designated as lost or stolen.', 'type' => 'User'),
            10051 => (object) array('reason' => 'Insufficient funds', 'explanation' => 'The card has insufficient funds to cover the cost of the transaction.', 'type' => 'Issuer'),
            10054 => (object) array('reason' => 'Expired card', 'explanation' => 'The payment gateway declined the transaction because the expiration date is expired or does not match.', 'type' => 'User'),
            10057 => (object) array('reason' => 'Function not permitted to cardholder', 'explanation' => 'The card issuer has declined the transaction as the credit card cannot be used for this type of transaction.', 'type' => 'Issuer'),
            10058 => (object) array('reason' => 'Function not permitted to terminal', 'explanation' => 'The card issuer has declined the transaction as the credit card cannot be used for this type of transaction.', 'type' => 'Issuer'),
            10061 => (object) array('reason' => 'Withdrawal limit exceeded', 'explanation' => 'Exceeds withdrawal amount limit.', 'type' => 'Issuer'),
            10062 => (object) array('reason' => 'Restricted card', 'explanation' => 'The customer\'s bank has declined their card.', 'type' => 'Issuer'),
            10063 => (object) array('reason' => 'Issuer response security violation', 'explanation' => 'Flag raised due to security validation problem.', 'type' => 'Issuer'),
            10065 => (object) array('reason' => 'Soft decline', 'explanation' => 'The issuer requests Strong Customer Authentication. The merchant should retry the transaction after successfully authenticating customer with 3DS first.', 'type' => 'Issuer'),
            10070 => (object) array('reason' => 'Call issuer', 'explanation' => 'Contact card issuer.', 'type' => 'Issuer'),
            10075 => (object) array('reason' => 'PIN entry tries exceeded', 'explanation' => 'Allowable number of PIN tries exceeded.', 'type' => 'User'),
            10076 => (object) array('reason' => 'Invalid / non-existent "to account" specified', 'explanation' => 'Invalid / non-existent OR Invalid / non-existent specified.', 'type' => 'System'),
            10096 => (object) array('reason' => 'System malfunction', 'explanation' => 'A temporary error occurred during the transaction.', 'type' => 'System'),
            10200 => (object) array('reason' => 'Generic error', 'explanation' => 'A temporary error occurred during the transaction.', 'type' => 'System'),
            10301 => (object) array('reason' => 'Soft decline', 'explanation' => 'The issuer requests Strong Customer Authentication. The merchant should retry the transaction after successfully authenticating customer with 3DS first.', 'type' => 'Issuer'),
        );
    }

    private function getEci() {
        $this->eci = (object) array(
            '0' =>	'Unspecified',
            '1' =>	'Authenticated',
            '2' =>	'No 3DS',
            '3' =>	'Attempt or not enrolled'
        );
    }


}

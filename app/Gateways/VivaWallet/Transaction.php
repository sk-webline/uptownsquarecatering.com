<?php

namespace App\Gateways\VivaWallet;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UriInterface;

class Transaction
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string[]
     */
    protected $transaction_status;

    /**
     * @var string[]
     */
    protected $transaction_type;

    /**
     * Constructor.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;

        $this->setTransactionStatus();

        $this->setTransactionType();
    }

    /**
     * Retrieve information about a transaction.
     *
     * @param  string  $transaction_id  The transaction id for which you wish to retrieve information.
     */
    public function get(string $transaction_id): ?\stdClass
    {
        return $this->client->get(
            $this->client->getApiUrl()->withPath("/checkout/v2/transactions/$transaction_id"),
            array_merge_recursive(
                $this->client->authenticateWithBearerToken()
            )
        );
    }

    /**
     * @return void
     */
    public function setTransactionStatus(): void
    {
        $this->transaction_status = [
            "F" =>	"The transaction has been completed successfully (PAYMENT SUCCESSFUL)",
            "A" =>	"The transaction is in progress (PAYMENT PENDING)",
            "C" =>	"The transaction has been captured (the C status refers to the original pre-auth transaction which has now been captured; the capture will be a separate transaction with status F)",
            "E" =>	"The transaction was not completed successfully (PAYMENT UNSUCCESSFUL)",
            "R" =>	"The transaction has been fully or partially refunded",
            "X" =>	"The transaction was cancelled by the merchant",
            "M" =>	"The cardholder has disputed the transaction with the issuing Bank",
            "MA" =>	"Dispute Awaiting Response",
            "MI" =>	"Dispute in Progress",
            "ML" =>	"A disputed transaction has been refunded (Dispute Lost)",
            "MW" =>	"Dispute Won",
            "MS" =>	"Suspected Dispute"
        ];
    }


    /**
     * @return void
     */
    public function setTransactionType(): void
    {
        $this->transaction_type = [
            "0" =>	"Card capture",
            "1" =>	"Card pre-auth",
            "4" =>	"Card refund 1",
            "5" =>	"Card charge",
            "6" =>	"Card charge (installments)",
            "7" =>	"Card void",
            "8" =>	"Card Original Credit (refund, betting MCC only) 1",
            "9" =>	"Viva Wallet charge",
            "11" =>	"Viva Wallet refund",
            "13" =>	"Card refund claimed",
            "15" =>	"Dias",
            "16" =>	"Cash",
            "17" =>	"Cash refund",
            "18" =>	"Card refund (installments) 1",
            "19" =>	"Card payout",
            "20" =>	"Alipay charge",
            "21" =>	"Alipay refund",
            "22" =>	"Card manual cash disbursement",
            "23" =>	"iDEAL charge",
            "24" =>	"iDEAL refund",
            "25" =>	"P24 charge",
            "26" =>	"P24 refund",
            "27" =>	"BLIK charge",
            "28" =>	"BLIK refund",
            "29" =>	"PayU charge",
            "30" =>	"PayU refund",
            "31" =>	"Card withdrawal",
            "32" =>	"MULTIBANCO charge",
            "34" =>	"giropay charge",
            "35" =>	"giropay refund",
            "36" =>	"Sofort charge",
            "37" =>	"Sofort refund",
            "38" =>	"EPS charge",
            "39" =>	"EPS refund",
            "40" =>	"WeChat Pay charge",
            "41" =>	"WeChat Pay refund",
            "42" =>	"BitPay charge",
            "44" =>	"SEPA Direct Debit charge",
            "45" =>	"SEPA Direct Debit refund",
            "48" =>	"PayPal charge",
            "49" =>	"PayPal refund",
            "50" =>	"Trustly charge",
            "51" =>	"Trustly refund",
            "52" =>	"Klarna charge",
            "53" =>	"Klarna refund",
            "58" =>	"Payconiq charge",
            "59" =>	"Payconiq refund",
            "60" =>	"IRIS charge",
            "61" =>	"IRIS refund",
            "62" =>	"Online Banking charge",
            "63" =>	"Online Banking refund"
        ];
    }
}

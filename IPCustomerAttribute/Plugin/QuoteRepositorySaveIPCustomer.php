<?php

namespace Training\IPCustomerAttribute\Plugin;

use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteRepository;

class QuoteRepositorySaveIPCustomer
{
    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    public function __construct(RemoteAddress $remoteAddress)
    {
        $this->remoteAddress = $remoteAddress;
    }

    public function beforeSave(QuoteRepository $subject, CartInterface $quote)
    {
        $ipCustomer = $this->remoteAddress->getRemoteAddress();

        $quote->setData('ip_customer', $ipCustomer);
    }
}
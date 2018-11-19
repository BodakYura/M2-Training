<?php

namespace Training\IPCustomerAttribute\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class SetCustomValuesToOrder implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $attribute = 'ip_customer';

        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $observer->getData('order');

        /** \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getData('quote');

        if ($quote->hasData($attribute)) {
            $order->setData($attribute, $quote->getData($attribute));
        }
    }
}

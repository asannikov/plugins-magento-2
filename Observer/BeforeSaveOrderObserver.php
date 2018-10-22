<?php

namespace LimeSoda\Cashpresso\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;

class BeforeSaveOrderObserver extends AbstractDataAssignObserver
{

    protected $request;

    protected $checkout;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \LimeSoda\Cashpresso\Api\Checkout $checkout
    )
    {
        $this->request = $request;

        $this->checkout = $checkout;
    }

    /**
     * @param Observer $observer
     * @throws \LimeSoda\Cashpresso\Gateway\Exception
     * @throws \Zend_Http_Client_Exception
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        $order = $event->getOrder();

        if ($order->getPayment()->getMethod() == 'cashpresso') {
            $purchaseId = $this->checkout->sendOrder($order);

            $order->getPayment()->setAdditionalInformation('purchaseId', $purchaseId);
        }
    }
}
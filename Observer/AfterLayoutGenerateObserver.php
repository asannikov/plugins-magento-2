<?php

namespace LimeSoda\Cashpresso\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;

class AfterLayoutGenerateObserver extends AbstractDataAssignObserver
{

    protected $request;

    protected $checkoutSession;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->request = $request;

        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        $actionName = $event->getFullActionName();

        if ($actionName == 'checkout_onepage_success') {
            $order = $this->checkoutSession->getLastRealOrder();

            $purchaseId = $order->getId() && $order->getPayment() && ($order->getPayment()->getMethod() == 'cashpresso') ?
                $order->getPayment()->getData(\Magento\Sales\Api\Data\OrderPaymentInterface::ADDITIONAL_INFORMATION . '/purchaseId') :
                null;

            if ($purchaseId) {
                /** @var \Magento\Framework\View\Layout $layout */
                $layout = $event->getLayout();

                if ($layout->getBlock('page.main.title')) {
                    $layout->getBlock('page.main.title')->setPageTitle(__('You have ordered but not yet paid'));
                }
            }
        }
    }
}
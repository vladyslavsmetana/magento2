<?php
namespace Smetana\Project\Observer;

use Magento\Framework\Event;
use Magento\Sales\Model\ResourceModel\Order\Grid;
use Smetana\Project\Helper\Data as HelperData;

/**
 * AddViewInitiator Observer Class
 *
 * @package Smetana\Project\Observer
 */
class AddViewInitiator implements Event\ObserverInterface
{
    /**
     * Helper Data instance
     *
     * @var HelperData
     */
    private $helperData;

    /**
     * Sales Order Grid Collection factory
     *
     * @var Grid\CollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @param HelperData $helperData
     * @param Grid\CollectionFactory $orderCollectionFactory
     */
    public function __construct(
        HelperData $helperData,
        Grid\CollectionFactory $orderCollectionFactory
    ) {
        $this->helperData = $helperData;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * Add call-centre Initiator before view Order
     *
     * @param Event\Observer $observer
     *
     * @return AddViewInitiator
     */
    public function execute(Event\Observer $observer): AddViewInitiator
    {
        if (HelperData::isSpecialist()) {
            $orderId = $observer->getData('controller_action')->getRequest()->getParam('order_id');
            /** @var Grid\Collection $orderCollection */
            $orderCollection = $this->orderCollectionFactory->create();
            $orderData = $orderCollection->addFieldToFilter('entity_id', ['eq' => $orderId])
                ->getFirstItem()
                ->getData();

            if (!empty($orderData)) {
                $this->helperData->addInitiatorToSpecificOrder(
                    $orderData,
                    (int)HelperData::getAdminUser()->getData('user_id')
                );
            }
        }

        return $this;
    }
}

<?php
namespace Smetana\Project\Model\Order;

use Magento\Sales\Model\ResourceModel\Order;

/**
 * Sales Order repository class
 *
 * @package Smetana\Project\Model\Order\Grid
 */
class Repository
{
    /**
     * Sales Order Collection instance
     *
     * @var Order\Collection
     */
    private $orderCollection;

    /**
     * Sales Order Collection factory
     *
     * @var Order\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param Order\CollectionFactory $collectionFactory
     */
    public function __construct(
        Order\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get Order Model by ID
     *
     * @param int $id
     *
     * @return mixed
     */
    public function getOrderById(int $id)
    {
        foreach ($this->getOrderCollection() as $gridOrder) {
            if ($gridOrder->getId() == $id) {
                return $gridOrder;
            }
        }
    }

    /**
     * Get Sales Order Collection
     *
     * @param void
     *
     * @return Order\Collection
     */
    public function getOrderCollection(): Order\Collection
    {
        if (null === $this->orderCollection) {
            /** @var Order\Collection orderCollection */
            $this->orderCollection = $this->collectionFactory->create();
        }

        return $this->orderCollection;
    }
}

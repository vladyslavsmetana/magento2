<?php
namespace Smetana\Project\Cron;

use Smetana\Project\Model\Order\Repository;
use Smetana\Project\Helper\Data as HelperData;
use Magento\Sales\Model\ResourceModel\Order\Grid\CollectionFactory as GridCollectionFactory;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\User\Model\User;

/**
 * Distribution of orders
 *
 * @package Smetana\Project\Cron
 */
class OrdersDistribution
{
    /**
     * There are no matching orders parameter
     *
     * @var String
     */
    const NO_FITTED_ORDERS = 'nothing';

    /**
     * Admin User Collection factory
     *
     * @var UserCollectionFactory
     */
    private $userCollectionFactory;

    /**
     * Sales Order Grid Collection factory
     *
     * @var GridCollectionFactory
     */
    private $gridCollectionFactory;

    /**
     * Product Collection factory instance
     *
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * Helper Data instance
     *
     * @var HelperData
     */
    private $helperData;

    /**
     * Sales Order repository instance
     *
     * @var Repository
     */
    private $orderRepository;

    /**
     * @param UserCollectionFactory $userCollectionFactory
     * @param GridCollectionFactory $gridCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param HelperData $helperData
     * @param Repository $orderRepository
     */
    public function __construct(
        UserCollectionFactory $userCollectionFactory,
        GridCollectionFactory $gridCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        HelperData $helperData,
        Repository $orderRepository
    ) {
        $this->userCollectionFactory = $userCollectionFactory;
        $this->gridCollectionFactory = $gridCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->helperData = $helperData;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Orders Distribution process
     *
     * @param void
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        while (true) {
            $email = '';
            while ($email != self::NO_FITTED_ORDERS) {
                $email = $this->assignOrders($email);
            }

            sleep(3);
        }
    }

    /**
     * Assign Order to call-centre specialist
     *
     * @param string $email
     *
     * @return string
     * @throws \Exception
     */
    private function assignOrders(string $email = ''): string
    {
        /** @var User|null $userFromQueue */
        $userFromQueue = $this->getUserFromQueue();

        if (!is_null($userFromQueue)) {
            /** @var \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $orderCollection */
            $orderCollection = $this->gridCollectionFactory->create();
            $orderCollection->addFieldToFilter('order_initiator', ['null' => true])
                ->setOrder('entity_id', 'DESC');

            if ($email != '') {
                $orderCollection->addFieldToFilter('customer_email', ['eq' => $email]);
            }

            foreach ($orderCollection as $order) {
                if (!$this->checkTime(
                    explode(' ', $order->getData('created_at'))[1],
                    $userFromQueue->getData('orders_type')
                )) {
                    continue;
                }

                if ($this->checkProductType($order->getId(), $userFromQueue->getData('products_type'))) {
                    $this->helperData->addInitiatorToSpecificOrder(
                        $order->getData(),
                        (int)$userFromQueue->getData('user_id')
                    );

                    return $order->getData('customer_email');
                }
            }

            if ($email != '') {
                $this->removeUserFromQueue($userFromQueue);
            }
        }

        return self::NO_FITTED_ORDERS;
    }

    /**
     * Get User Model from queue
     *
     * @param void
     *
     * @return User|null
     */
    private function getUserFromQueue()
    {
        $userModel = null;
        /** @var \Magento\User\Model\ResourceModel\User\Collection $userCollection */
        $userCollection = $this->userCollectionFactory->create();
        $userCollection->addFieldToFilter('need_order', ['notnull' => true])
            ->setOrder('need_order', 'ASC');

        if ($userCollection->getSize() > 0) {
            /** @var User $userModel */
            $userModel = $userCollection->getFirstItem();
        }

        return $userModel;
    }

    /**
     * Remove specific user from distribution queue
     *
     * @param User $userFromQueue
     *
     * @return OrdersDistribution
     * @throws \Exception
     */
    private function removeUserFromQueue(User $userFromQueue): OrdersDistribution
    {
        $userFromQueue->setData('need_order', null)->save();

        return $this;
    }

    /**
     * Compare provided time with allowed
     *
     * @param string $createdAt
     * @param string $timeType
     *
     * @return bool
     */
    private function checkTime(string $createdAt, string $timeType = ''): bool
    {
        $eight = '08:00:00';
        $twenty = '20:00:00';
        $allowed = true;

        switch ($timeType) {
            case 'night':
                $allowed = $createdAt > $twenty || $createdAt < $eight;
                break;
            case 'day':
                $allowed = $createdAt > $eight && $createdAt < $twenty;
                break;
        }

        return $allowed;
    }

    /**
     * Check product type according to user data
     *
     * @param int $orderId
     * @param string $userProductType
     *
     * @return bool
     */
    private function checkProductType(int $orderId, string $userProductType = ''): bool
    {
        $orderProducts = [];
        /** @var \Magento\Sales\Model\Order $orderModel */
        $orderModel = $this->orderRepository->getOrderById($orderId);

        foreach ($orderModel->getAllItems() as $item) {
            $orderProducts[] = $item->getData('product_id');
        }

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', ['in' => $orderProducts]);

        foreach ($this->getFilterData($userProductType) as $filter) {
            $collection->addFieldToFilter('product_types', $filter);
        }

        return $collection->getSize() > 0;
    }

    /**
     * Get data to filter Collection
     *
     * @param string $userProductType
     *
     * @return array
     */
    private function getFilterData(string $userProductType): array
    {
        $largeAppliances = 'large_appliances';
        $smallAppliances = 'small_appliances';
        $gadgets = 'gadgets';
        $filter = [];

        switch ($userProductType) {
            case $largeAppliances:
                $filter = [['in' => $largeAppliances]];
                break;
            case $smallAppliances:
                $filter = [
                    ['in' => $smallAppliances],
                    ['nin' => [$largeAppliances]],
                ];
                break;
            case $gadgets:
                $filter = [
                    ['in' => $gadgets],
                    ['nin' => [$largeAppliances, $smallAppliances]],
                ];
                break;
            default:
                $filter = [['nin' => [$largeAppliances, $smallAppliances, $gadgets]]];
        }

        return $filter;
    }
}

<?php
namespace Smetana\Project\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\User\Model\ResourceModel\User;
use Smetana\Project\Block\Options;
use Smetana\Project\Helper\Data as HelperData;

/**
 * Set distribution queue action class
 *
 * @package Smetana\Project\Controller\Adminhtml\Order
 */
class SetQueue extends Action
{
    /**
     * Admin User Collection factory
     *
     * @var User\CollectionFactory
     */
    private $userCollectionFactory;

    /**
     * @param Action\Context $context
     * @param User\CollectionFactory $userCollectionFactory
     */
    public function __construct(
        Action\Context $context,
        User\CollectionFactory $userCollectionFactory
    ) {
        $this->userCollectionFactory = $userCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Set distribution queue
     *
     * @param void
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function execute(): ResponseInterface
    {
        $queueColumn = 'need_order';
        /** @var User\Collection $users */
        $users = $this->userCollectionFactory->create();
        $lastInQueue = max($users->getColumnValues($queueColumn)) ?? 0;

        HelperData::getAdminUser()->setData($queueColumn, ++$lastInQueue)->save();
        return $this->_redirect(Options::PATH_TO_ORDER_GRID, ['disabled' => true]);
    }
}

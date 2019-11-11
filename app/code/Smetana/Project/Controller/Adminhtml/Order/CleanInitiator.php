<?php
namespace Smetana\Project\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\ResourceConnection;
use Smetana\Project\Helper\Data as HelperData;
use Smetana\Project\Block\Options;

/**
 * Clean Initiator action class
 *
 * @package Smetana\Project\Controller\Adminhtml\Order
 */
class CleanInitiator extends Action
{
    /**
     * Resource Connection instance
     *
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param Action\Context $context
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Action\Context $context,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    /**
     * Execute clean order initiator
     *
     * @param void
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        if (HelperData::isCoordinator()) {
            $requestParams = $this->getRequest()->getParams();
            $orderId = $requestParams['order_id']  ?? $requestParams['selected'] ?? [];

            if (!is_array($orderId)) {
                $orderId = [$orderId];
            }

            $connection = $this->resourceConnection->getConnection();
            foreach ($orderId as $id) {
                $connection->query("update sales_order_grid set order_initiator=null where entity_id=$id;");
            }

            $this->messageManager->addSuccessMessage(__('You cleaned %1 initiator(s).', count($orderId)));
        }

        return $this->_redirect(Options::PATH_TO_ORDER_GRID);
    }
}

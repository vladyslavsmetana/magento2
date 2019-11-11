<?php
namespace Smetana\Project\Model\Order;

use Magento\Framework\Registry;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;
use Smetana\Project\Helper\Data as HelperData;

/**
 * Change order grid Collection class
 *
 * @package Smetana\Project\Model\Order
 */
class GridCollection extends Collection
{
    /**
     * Filter Order grid Collection
     *
     * @param void
     *
     * @return void
     */
    protected function _renderFiltersBefore(): void
    {
        $filter = $this->getCollectionFilter();

        if (!empty($filter)) {
            $this->addFieldToFilter($filter['field'], $filter['value']);
        }

        parent::_renderFiltersBefore();
    }

    /**
     * Retrieve Collection filter
     *
     * @param
     *
     * @return array
     */
    private function getCollectionFilter(): array
    {
        $emailFilter = ObjectManager::getInstance()->get(Registry::class)->registry('email_filter');
        $filter = [];

        switch (true) {
            case HelperData::isSpecialist() && !is_null($emailFilter):
                $filter = ['field' => 'customer_email', 'value' => ['eq' => $emailFilter]];
                break;
            case HelperData::isSpecialist():
                $filter = ['field' => 'order_initiator', 'value' => ['eq' => HelperData::getAdminUser()->getData('user_id')]];
                break;
            case HelperData::isCoordinator():
                $filter = ['field' => 'order_initiator', 'value' => ['notnull' => true]];
                break;
        }

        return $filter;
    }

    /**
     * Check number Collection items before skip reload
     *
     * @param void
     *
     * @return mixed
     */
    public function getItems()
    {
        $this->load();
        if ($this->getMainTable() == 'sales_order_grid') {
            $this->_eventManager->dispatch('skip_page_reload_event', ['skip' => count($this->_items) > 0]);
        }

        return $this->_items;
    }
}

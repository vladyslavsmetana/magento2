<?php
namespace Smetana\Project\Block\Adminhtml\Order;

use Magento\Sales\Model\ResourceModel\Order\Grid;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;
use Smetana\Project\Helper\Data as HelperData;

/**
 * Class Order Grid button
 *
 * @package Smetana\Project\Block\Adminhtml\Order
 */
class GridButton implements ButtonProviderInterface
{
    /**
     * Url Builder instance
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Sales Order Grid Collection factory
     *
     * @var Grid\CollectionFactory
     */
    private $gridCollectionFactory;

    /**
     * Helper Data instance
     *
     * @var HelperData
     */
    private $helperData;

    /**
     * @param Context $context
     * @param Grid\CollectionFactory $gridCollectionFactory
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        Grid\CollectionFactory $gridCollectionFactory,
        HelperData $helperData
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->gridCollectionFactory = $gridCollectionFactory;
        $this->helperData = $helperData;
    }

    /**
     * Get button parameters
     *
     * @param void
     *
     * @return array
     */
    public function getButtonData(): array
    {
        $data = [];
        if (HelperData::isSpecialist()) {
            /** @var Grid\Collection $collection */
            $collection = $this->gridCollectionFactory->create();
            $collection->addFieldToFilter('order_initiator', ['eq' => HelperData::getAdminUser()->getData('user_id')]);

            if (!in_array('pending', $collection->getColumnValues('status'))) {
                $isButtonDisabled = $this->helperData->isButtonDisabled();
                $data = [
                    'label' => $isButtonDisabled
                        ? __('Waiting for the order')
                        : __('Get Order'),
                    'class' => 'primary',
                    'disabled' => $isButtonDisabled,
                    'on_click' => 'setLocation(\'' . $this->getUrl('smetana_project_admin/order/setqueue') . '\')',
                    'sort_order' => -1,
                ];
            }
        }

        return $data;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     *
     * @return string
     */
    private function getUrl($route = '', $params = []): string
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}

<?php
namespace Smetana\Project\Block\Adminhtml\Order;

use Smetana\Project\Block\Options;
use Smetana\Project\Helper\Data as HelperData;
use Magento\Sales\Block\Adminhtml\Order\View;

/**
 * Sales Order view rewrite
 *
 * Class Smetana_Project_Block_Adminhtml_Order_View
 */
class ViewButton extends View
{
    /**
     * Add button to Order view page
     *
     * @oaram void
     *
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();

        if (HelperData::isCoordinator()) {
            $message = __('Are you sure you want to clean the initiator for this Order?');
            $this->addButton(
                'clean_initiator',
                [
                    'label' => __('Clean initiator'),
                    'onclick' => "confirmSetLocation('{$message}', 
                    '{$this->getUrl(Options::PATH_TO_REMOVE_INITIATOR, ['order_id' => $this->getOrderId()])}')"
                ]
            );
        }
    }
}

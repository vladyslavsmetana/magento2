<?php
namespace Smetana\Project\Plugin;

use Smetana\Project\Helper\Data as HelperData;
use Magento\Framework\Registry;
use Magento\Framework\Api\Filter;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;

/**
 * Override filter data
 *
 * @package Smetana\Project\Plugin
 */
class SetEmailFilter
{
    /**
     * Change type for email filter
     *
     * @param DataProvider $pluginObject
     * @param Filter $filter
     *
     * @return void
     */
    public function beforeAddFilter(DataProvider $pluginObject, Filter $filter): void
    {
        if (
            HelperData::isSpecialist()
            && $filter->getField() == 'customer_email'
        ) {
            $filter->setData('condition_type', 'fulltext');
            ObjectManager::getInstance()
                ->get(Registry::class)
                ->register('email_filter', trim($filter->getValue(), '%'));
        }
    }
}

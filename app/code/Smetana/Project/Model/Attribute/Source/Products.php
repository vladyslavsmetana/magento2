<?php
namespace Smetana\Project\Model\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Smetana Project product types config
 *
 * @package Smetana\Project\Model\Attribute\Source
 */
class Products extends AbstractSource
{
    /**
     * Retrieve product options
     *
     * @param void
     *
     * @return array
     */
    public function getAllOptions(): array
    {
        if (!$this->_options) {
            foreach ($this->getAttributeData() as $value => $label) {
                $this->_options[] = ['value' => $value, 'label' => $label];
            }
        }

        return $this->_options;
    }

    /**
     * Retrieve raw User attribute data
     *
     * @param void
     *
     * @return array
     */
    public function getAttributeData(): array
    {
        return [
            'non-selected'     => __('Not specified'),
            'large_appliances' => __('Large home appliances'),
            'small_appliances' => __('Small household appliances'),
            'gadgets'          => __('Gadgets'),
        ];
    }
}

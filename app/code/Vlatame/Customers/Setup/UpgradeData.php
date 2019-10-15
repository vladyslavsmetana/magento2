<?php
namespace Vlatame\Customers\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Vlatame\Customers\Block\AttributeNames;

/**
 * Class UpgradeData
 *
 * @package Bibhu\Customattribute\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

//        if (version_compare($context->getVersion(), '1.0.1') < 0) {
//            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
//            $attributes = [AttributeNames::RESTRICTION_ENABLE, AttributeNames::COUNTRIES_RESTRICTION];
//            foreach ($attributes as $attribute) {
//                $eavSetup->removeAttribute(
//                    \Magento\Customer\Model\Customer::ENTITY,
//                    $attribute
//                );
//            }
//        }

        $setup->endSetup();
    }
}

<?php
namespace Vlatame\Customers\Setup;

use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Vlatame\Customers\Block\AttributeNames;

/**
 * Customer attribute setup
 *
 * @package Vlatame\Customers\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV Config model
     *
     * @var Config
     */
    private $eavConfig;

    /**
     * EAV setup factory instance
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param Config $eavConfig
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        Config $eavConfig,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavConfig           = $eavConfig;
        $this->eavSetupFactory     = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (!array_key_exists(AttributeNames::RESTRICTION_ENABLE, $this->eavConfig->getEntityAttributes('customer'))) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                Customer::ENTITY,
                AttributeNames::RESTRICTION_ENABLE,
                [
                    'type' => 'varchar',
                    'input' => 'select',
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'default' => '0',
                    'label' => 'Delivery Restriction',
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'system' => false,
                    'group' => 'General',
                    'global' => true,
                    'visible_on_front' => true,
                ]
            );

            $eavSetup->addAttribute(
                Customer::ENTITY,
                AttributeNames::COUNTRIES_RESTRICTION,
                [
                    'type' => 'varchar',
                    'input' => 'multiselect',
                    'source' => \Magento\Customer\Model\ResourceModel\Address\Attribute\Source\Country::class,
                    'label' => 'Country Limit',
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'system' => false,
                    'group' => 'General',
                    'global' => true,
                    'visible_on_front' => true,
                ]
            );

            $attributeNames = [AttributeNames::RESTRICTION_ENABLE, AttributeNames::COUNTRIES_RESTRICTION];
            foreach ($attributeNames as $name) {
                $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, $name);
                $attribute->setData(
                    'used_in_forms',
                    ['adminhtml_customer', 'customer_account_create', 'customer_account_edit']
                )->save();
            }
        }

        $setup->endSetup();
    }
}

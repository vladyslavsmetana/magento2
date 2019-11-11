<?php
namespace Smetana\Project\Setup;

use Smetana\Project\Block\Options;
use Magento\Eav\Setup;
use Magento\Authorization\Model;
use Magento\Catalog\Model\Product;
use Magento\Framework\DB\Ddl\Table;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollectionFactory;

/**
 * Attributes and roles setup class
 *
 * @package Smetana\Project\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * User Role Model factory
     *
     * @var Model\RoleFactory
     */
    private $userRoleFactory;

    /**
     * Attribute Set factory instance
     *
     * @var Attribute\SetFactory
     */
    private $attributeSetFactory;

    /**
     * Attribute Set Collection factory instance
     *
     * @var AttributeSetCollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * EAV setup factory instance
     *
     * @var Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param Model\RoleFactory $userRoleFactory
     * @param Attribute\SetFactory $attributeSetFactory
     * @param AttributeSetCollectionFactory $attributeCollectionFactory
     * @param Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        Model\RoleFactory $userRoleFactory,
        Attribute\SetFactory $attributeSetFactory,
        AttributeSetCollectionFactory $attributeCollectionFactory,
        Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->userRoleFactory = $userRoleFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $userAttributes = ['orders_type', 'products_type', 'need_order'];

        foreach ($userAttributes as $userAttribute) {
            $setup->getConnection()->addColumn(
                $setup->getTable('admin_user'),
                $userAttribute,
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => null,
                    'comment' => $userAttribute,
                ]
            );
        }

        /** @var Model\Role $userRoleModel */
        $userRoleModel = $this->userRoleFactory->create();
        $adminRoles = [Options::SPECIALIST_ROLE_NAME, Options::COORDINATOR_ROLE_NAME];

        foreach ($adminRoles as $role) {
            $userRoleModel->setData(['name' => $role, 'role_type' => 'G'])->save();
        }

        $orderAttributes = ['order_initiator', 'order_primary_initiator'];

        foreach ($orderAttributes as $orderAttribute) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_grid'),
                $orderAttribute,
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => $orderAttribute,
                ]
            );
        }

        $attributeNames = $this->attributeCollectionFactory->create()
            ->getColumnValues('attribute_set_name');

        if (!in_array(Options::PRODUCT_ATTRIBUTE_SET, $attributeNames)) {
            /** @var Attribute\Set $attributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            /** @var Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $defaultSetId = $eavSetup->getDefaultAttributeSetId(Product::ENTITY);

            $attributeSet->setData([
                'entity_type_id' => $defaultSetId,
                'attribute_set_name' => Options::PRODUCT_ATTRIBUTE_SET
            ])->save();

            $attributeSet->initFromSkeleton($defaultSetId)->save();

            $eavSetup->addAttribute(
                Product::ENTITY,
                'product_types',
                [
                    'attribute_set' => Options::PRODUCT_ATTRIBUTE_SET,
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'sort_order' => '0',
                    'label' => __('Type of Product'),
                    'input' => 'select',
                    'source' => \Smetana\Project\Model\Attribute\Source\Products::class,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '0',
                    'searchable' => false,
                    'filterable' => true,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'visible_in_advanced_search' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                ]
            );

            $eavSetup->addAttributeToSet(
                $defaultSetId,
                Options::PRODUCT_ATTRIBUTE_SET,
                'General',
                'product_types'
            );
        }

        $setup->endSetup();
    }
}

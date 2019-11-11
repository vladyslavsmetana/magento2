<?php
namespace Smetana\Project\Block\Adminhtml\User;

use Magento\User\Model\User;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\App\ObjectManager;
use Magento\User\Block\User\Edit\Tab\Main;
use Smetana\Project\Model\Attribute\Source\Products;
use Smetana\Project\Block\Options;

/**
 * Change admin user edit form
 *
 * @package Smetana\Project\Block\Adminhtml\User
 */
class Edit extends Main
{
    /**
     * Add fields to Admin user edit form
     *
     * @param void
     *
     * @return Form
     */
    protected function _prepareForm(): Form
    {
        $parent = parent::_prepareForm();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->getForm();
        $fieldset = $form->addFieldset(
            'callcentre_fieldset',
            ['legend' => __(Options::SPECIALIST_ROLE_NAME)]
        );

        $fieldset->addField(
            'orders_type',
            'select',
            [
                'name' => 'orders_type',
                'label' => __('Order Types'),
                'id' => 'orders_type',
                'title' => __('Order Types'),
                'class' => 'input-select',
                'style' => 'width: 180px',
                'options' => [
                    'non-selected' => __('Не указан'),
                    'night' => __('Nightly - (from 20.00 to 08.00)'),
                    'day' => __('Daytime - (from 08.00 to 20.00)'),
                ],
            ]
        );

        $fieldset->addField(
            'products_type',
            'select',
            [
                'name' => 'products_type',
                'label' => __('Product Types'),
                'id' => 'products_type',
                'title' => __('Product Types'),
                'class' => 'input-select',
                'style' => 'width: 180px',
                'options' => ObjectManager::getInstance()->get(Products::class)->getAttributeData(),
            ]
        );

        /** @var $model User */
        $model = $this->_coreRegistry->registry('permissions_user');
        $data = $model->getData();
        unset($data['password']);
        $form->setValues($data);
        $this->setForm($form);

        return $parent;
    }
}

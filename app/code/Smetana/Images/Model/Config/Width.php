<?php
namespace Smetana\Images\Model\Config;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Smetana\Images\Model\Image\Delete;

/**
 * Checking Width of ImageTest
 */
class Width extends Value
{
    /**
     * Delete Images Model
     *
     * @var Delete
     */
    private $deleteImageModel;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param Delete|null $deleteImageModel
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        Delete $deleteImageModel = null
    ) {
        $this->deleteImageModel = $deleteImageModel
            ?? ObjectManager::getInstance()->get(Delete::class);
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * ImageTest width change check
     *
     * @return \Magento\Framework\App\Config\Value
     */
    public function beforeSave()
    {
        if ($this->isValueChanged()) {
            $this->deleteImageModel->deleteImage(Delete::RESIZE_PATH);
        }

        return parent::beforeSave();
    }
}

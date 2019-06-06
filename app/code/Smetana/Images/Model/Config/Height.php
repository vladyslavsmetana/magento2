<?php
namespace Smetana\Images\Model\Config;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Checking Image Operations
 */
class Height extends Value
{
    /**
     * Filesystem
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * File Operations Class
     *
     * @var File
     */
    private $fileDriver;

    /**
     * @param Filesystem $filesystem
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param File|null $fileDriver
     */
    public function __construct(
        Filesystem $filesystem,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        File $fileDriver = null
    ) {
        $this->filesystem = $filesystem;
        $this->fileDriver = $fileDriver
            ?? ObjectManager::getInstance()->get(File::class);
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
     * Image height change check
     */
    public function beforeSave()
    {
        if ($this->isValueChanged()) {
            $mediaDirectory = $this->filesystem->getDirectoryRead('media');
            $resizePath = $mediaDirectory->getAbsolutePath() . 'products_image/resize/';
            if ($this->fileDriver->isExists($resizePath)) {
                $files = $this->fileDriver->readDirectory($resizePath);
                if ($files) {
                    foreach ($files as $file) {
                        $this->fileDriver->deleteFile($file);
                    }
                }
            }
        }
    }
}

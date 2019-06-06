<?php
namespace Smetana\Images\Model\Config;

use Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\MediaStorage\Model\File\UploaderFactory;

/**
 * Image Operations
 */
class Image extends \Magento\Config\Model\Config\Backend\Image
{
    /**
     * File Operations
     *
     * @var File
     */
    private $fileDriver;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param UploaderFactory $uploaderFactory
     * @param RequestDataInterface $requestData
     * @param Filesystem $filesystem
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param File|null $fileDriver
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        UploaderFactory $uploaderFactory,
        RequestDataInterface $requestData,
        Filesystem $filesystem,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        File $fileDriver = null
    ) {
        $this->fileDriver = $fileDriver
            ?? ObjectManager::getInstance()->get(File::class);
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $uploaderFactory,
            $requestData,
            $filesystem,
            $resource,
            $resourceCollection,
            $data
        );
    }


    /**
     * Changing process of saving image
     *
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $folders = ['original', 'resize'];

        if (!empty($this->getFileData())) {
            $mimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

            $correctMime = false;
            foreach ($mimeTypes as $type) {
                if (mime_content_type($this->getFileData()['tmp_name']) != $type) {
                    continue;
                }
                $correctMime = true;
                break;
            }
            if ($correctMime === false) {
                throw new LocalizedException(__('%1', 'The file has the wrong extension'));
            }

            if (!$this->fileDriver->isExists($this->_getUploadDir())) {
                foreach ($folders as $folder) {
                    $this->fileDriver
                        ->createDirectory($this->_mediaDirectory->getAbsolutePath("products_image/$folder"));
                }
            }
        }

        if (!empty($this->getFileData())
            || array_key_exists(
                'delete',
                $this->_data["groups"]["smetana_group"]["fields"]["smetana_upload_image"]["value"]
            )
        ) {
            foreach ($folders as $folder) {
                $files = $this->fileDriver->readDirectory($this->_mediaDirectory->getAbsolutePath("products_image/$folder"));
                if ($files) {
                    foreach ($files as $file) {
                        $this->fileDriver->deleteFile($file);
                    }
                }
            }
        }

        return parent::beforeSave();
    }
}

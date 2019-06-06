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
    private $file;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param UploaderFactory $uploaderFactory
     * @param RequestDataInterface $requestData
     * @param Filesystem $filesystem
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     * @param File $file
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
        File $file = null
    ) {
        $this->file = $file
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
        if (!empty($this->getFileData())) {
            $mimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

            $correctMime = null;
            foreach ($mimeTypes as $type) {
                if (mime_content_type($this->getFileData()['tmp_name']) != $type) {
                    continue;
                }
                $correctMime = true;
                break;
            }
            if ($correctMime === null) {
                throw new LocalizedException(__('%1', 'The file has the wrong extension'));
            }

            if (!$this->file->isReadable($this->_getUploadDir())) {
                $this->file->createDirectory($this->_mediaDirectory->getAbsolutePath() . 'products_image/original');
            }
        }

        if (!empty($this->getFileData())
            || array_key_exists(
                'delete',
                $this->_data["groups"]["smetana_group"]["fields"]["smetana_upload_image"]["value"]
            )
        ) {
            $files = $this->file->readDirectory($this->_getUploadDir());
            if ($files) {
                foreach ($files as $file) {
                    $this->file->deleteFile($file);
                }
            }
        }

        return parent::beforeSave();
    }
}

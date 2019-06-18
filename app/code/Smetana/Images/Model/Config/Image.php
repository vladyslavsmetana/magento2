<?php
namespace Smetana\Images\Model\Config;

use Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface;
use Magento\Config\Model\Config\Backend\Image as Images;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Smetana\Images\Model\Image\Delete;

/**
 * Image Saving Operations
 */
class Image extends Images
{
    /**
     * Mime type .jpeg
     *
     * @var string
     */
    const MIME_TYPE_JPEG = 'image/jpeg';

    /**
     * Mime type .png
     *
     * @var string
     */
    const MIME_TYPE_PNG = 'image/png';

    /**
     * Mime type .gif
     *
     * @var string
     */
    const MIME_TYPE_GIF = 'image/gif';

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
     * @param UploaderFactory $uploaderFactory
     * @param RequestDataInterface $requestData
     * @param Filesystem $filesystem
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
        UploaderFactory $uploaderFactory,
        RequestDataInterface $requestData,
        Filesystem $filesystem,
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
            $uploaderFactory,
            $requestData,
            $filesystem,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Checking mime type of saving image
     *
     * @throws LocalizedException
     */
    private function checkMimeType()
    {
        $mimeTypes = [self::MIME_TYPE_JPEG, self::MIME_TYPE_PNG, self::MIME_TYPE_GIF];

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
    }

    /**
     * Operations before saving image
     */
    public function beforeSave()
    {
        if (!empty($this->getFileData())) {
            $this->checkMimeType();
        }
        $folders = [Delete::RESIZE_PATH, Delete::ORIG_PATH];

        if (!empty($this->getFileData())
            || $this->getValue('delete')
        ) {
            foreach ($folders as $folder) {
                $this->deleteImageModel->deleteImage($folder);
            }
        }

        return parent::beforeSave();
    }
}

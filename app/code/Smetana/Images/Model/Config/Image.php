<?php
namespace Smetana\Images\Model\Config;

use Magento\Framework\App\ObjectManager;

/**
 * Image Operations
 */
class Image extends \Magento\Config\Model\Config\Backend\Image
{
    /**
     * The tail part of directory path for uploading
     *
     * @var string
     */
    const UPLOAD_DIR = 'products_image';

    /**
     * File Operations
     *
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $file;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface $requestData
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @param \Magento\Framework\Filesystem\Driver\File $file
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface $requestData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Framework\Filesystem\Driver\File $file = null
    ) {
        $this->file = $file
            ?? ObjectManager::getInstance()->get(\Magento\Framework\Filesystem\Driver\File::class);
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
     * Returning path to directory for upload file
     *
     * @return string
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    protected function _getUploadDir(): string
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }

    /**
     * Making a decision about whether to add info about the scope
     *
     * @return boolean
     */
    protected function _addWhetherScopeInfo(): bool
    {
        return true;
    }

    /**
     * Changing process of saving image
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        if (!empty($this->getFileData())) {
            if (!$this->file->isReadable($this->_getUploadDir())) {
                $folders = ['default', 'resize'];
                foreach ($folders as $folder) {
                    $this->file->createDirectory($this->_mediaDirectory->getAbsolutePath() . 'products_image/' . $folder);
                }
            }
            $files = $this->file->readDirectory($this->_getUploadDir());
            if ($files) {
                foreach ($files as $file) {
                    $this->file->deleteFile($file);
                }
                if (mime_content_type($this->getFileData()['tmp_name']) != 'image/jpeg') {
                    throw new \Magento\Framework\Exception\LocalizedException(__('%1', 'The file has the wrong extension'));
                }
            }
        }

        return parent::beforeSave();
    }
}

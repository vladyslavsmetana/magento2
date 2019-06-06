<?php
namespace Smetana\Images\Model\Frontend;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Image\AdapterFactory;

/**
 * Images helper
 */
class Resize extends AbstractHelper
{
    /**
     * Filesystem
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Adapter Factory
     *
     * @var AdapterFactory
     */
    private $imageFactory;

    /**
     * File Operations Class
     *
     * @var File
     */
    private $file;

    /**
     * @param Filesystem $filesystem
     * @param AdapterFactory $imageFactory
     * @param File $file
     * @param Context $context
     */
    public function __construct(
        Filesystem $filesystem,
        AdapterFactory $imageFactory,
        File $file,
        Context $context
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->file = $file;
    }

    /**
     * Resizing Smetana Images
     *
     * @param string $image
     * @param int $width
     * @param int $height
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     *
     * @return string
     */
    public function resize(string $image, int $width = null, int $height = null): string
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead('media');
        $origPath = $mediaDirectory->getAbsolutePath('products_image/original/' . $image);
        $resizePath = $mediaDirectory->getAbsolutePath() . 'products_image/resize/';
        $destinationPath = $resizePath . $width . $height . '_' . $image;
        if (!$this->file->isFile($origPath)) {
            return '';
        }

        if (!$this->file->isFile($destinationPath)) {
            if (!$this->file->isReadable($resizePath)) {
                $this->file->createDirectory($resizePath);
            }

            $files = $this->file->readDirectory($resizePath);
            if ($files) {
                foreach ($files as $file) {
                    $this->file->deleteFile($file);
                }
            }
            $imageResize = $this->imageFactory->create();
            $imageResize->open($origPath);
            $imageResize->constrainOnly(true);
            $imageResize->keepTransparency(true);
            $imageResize->resize($width, $height);
            $imageResize->save($destinationPath);
        }

        return $mediaDirectory->getRelativePath($destinationPath);
    }
}

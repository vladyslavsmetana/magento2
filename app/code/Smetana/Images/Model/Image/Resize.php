<?php
namespace Smetana\Images\Model\Image;

use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Image\AdapterFactory;
use Smetana\Images\Model\Image\Delete;

/**
 * ImageTest Resizing Operations
 */
class Resize
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
    private $fileDriver;

    /**
     * @param Filesystem $filesystem
     * @param AdapterFactory $imageFactory
     * @param File $fileDriver
     */
    public function __construct(
        Filesystem $filesystem,
        AdapterFactory $imageFactory,
        File $fileDriver
    ) {
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->fileDriver = $fileDriver;
    }

    /**
     * Resizing Images
     *
     * @param string $image
     * @param int $width
     * @param int $height
     *
     * @return string
     */
    public function resize(string $image, int $width = null, int $height = null): string
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead('media');
        $origPath = $mediaDirectory->getAbsolutePath(Delete::ORIG_PATH . $image);
        $destinationPath = $mediaDirectory->getAbsolutePath() . Delete::RESIZE_PATH . $width . $height . '_' . $image;
        if (!$this->fileDriver->isExists($origPath)) {
            return '';
        }

        if (!$this->fileDriver->isExists($destinationPath)) {
            $imageResize = $this->imageFactory->create();
            $imageResize->open($origPath);
            $imageResize->resize($width, $height);
            $imageResize->save($destinationPath);
        }

        return $mediaDirectory->getRelativePath($destinationPath);
    }
}

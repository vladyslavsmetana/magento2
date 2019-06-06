<?php
namespace Smetana\Images\Model\Frontend;

use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Image\AdapterFactory;

/**
 * Images helper
 */
class Resize
{
    const ORIG_PATH = 'products_image/original/';
    const RESIZE_PATH = 'products_image/resize/';

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
        $origPath = $mediaDirectory->getAbsolutePath(self::ORIG_PATH . $image);
        $destinationPath = $mediaDirectory->getAbsolutePath() . self::RESIZE_PATH . $width . $height . '_' . $image;
        if (!$this->fileDriver->isExists($origPath)) {
            return '';
        }

        if (!$this->fileDriver->isExists($destinationPath)) {
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

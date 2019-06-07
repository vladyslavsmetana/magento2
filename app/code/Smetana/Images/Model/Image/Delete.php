<?php
namespace Smetana\Images\Model\Image;

use Magento\Framework\Filesystem;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\Driver\File;

/**
 * Deleting images from specified folders
 */
class Delete
{
    /**
     * Path to Original folder
     *
     * @var String
     */
    const ORIG_PATH = 'products_image/original/';

    /**
     * Path to Resize folder
     *
     * @var String
     */
    const RESIZE_PATH = 'products_image/resize/';

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
     * @param File|null $fileDriver
     */
    public function __construct(
        Filesystem $filesystem,
        File $fileDriver = null
    ) {
        $this->filesystem = $filesystem;
        $this->fileDriver = $fileDriver
            ?? ObjectManager::getInstance()->get(File::class);
    }

    /**
     * Deleting Images
     *
     * @param string $path
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function deleteImage(string $path)
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead('media');
        $imagePath = $mediaDirectory->getAbsolutePath($path);
        if ($this->fileDriver->isExists($imagePath)) {
            if ($files = $this->fileDriver->readDirectory($imagePath)) {
                foreach ($files as $file) {
                    $this->fileDriver->deleteFile($file);
                }
            }
        }
    }
}

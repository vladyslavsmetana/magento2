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
    const ORIG_PATH = 'smetana/original/';

    /**
     * Path to Resize folder
     *
     * @var String
     */
    const RESIZE_PATH = 'smetana/resize/';

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
     * @param File $fileDriver
     */
    public function __construct(
        Filesystem $filesystem,
        File $fileDriver
    ) {
        $this->filesystem = $filesystem;
        $this->fileDriver = $fileDriver;
    }

    /**
     * Deleting Images
     *
     * @param string $path
     *
     * @return void
     */
    public function deleteImage(string $path): void
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead('media');
        $imagePath = $mediaDirectory->getAbsolutePath($path);
        if ($this->fileDriver->isExists($imagePath)) {
            $files = $this->fileDriver->readDirectory($imagePath);
                foreach ($files as $file) {
                    $this->fileDriver->deleteFile($file);
                }
        }
    }
}

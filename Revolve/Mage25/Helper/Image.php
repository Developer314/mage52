<?php

namespace Revolve\Mage25\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Custom directory relative to the "media" folder
     */
    const DIRECTORY = '';

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * @var \Magento\Framework\Image\Factory
     */
    protected $_imageFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\Factory $imageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Image\AdapterFactory $imageFactory
    ) {
	    
	     parent::__construct($context);
	    
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
        
        
       
    }

    /**
     * First check this file on FS
     *
     * @param string $filename
     * @return bool
     */
    protected function _fileExists($filename)
    {
        if ($this->_mediaDirectory->isFile($filename)) {
            return true;
        }
        return false;
    }

    /**
     * Resize image
     * @return string
     */
    public function resize($image, $width = null, $height = null)
{
    $mediaFolder = self::DIRECTORY;

    $path = $mediaFolder . 'cache';
    if ($width !== null) {
        $path .= '/' . $width . 'x';
        if ($height !== null) {
            $path .= $height ;
        }
    }

    $absolutePath = $this->_mediaDirectory->getAbsolutePath($mediaFolder) . $image;
    $imageResized = $this->_mediaDirectory->getAbsolutePath($path) . $image;

    if (!$this->_fileExists($path . $image) && $this->_fileExists($mediaFolder . $image)) {
        $imageFactory = $this->_imageFactory->create();
        $imageFactory->open($absolutePath);
        $imageFactory->constrainOnly(true);
        $imageFactory->keepAspectRatio(true);
        $imageFactory->keepFrame(false);

        $originalWidth = $imageFactory->getOriginalWidth();
        $originalHeight = $imageFactory->getOriginalHeight();

        $oldAspectRatio = $originalWidth / $originalHeight;
        $newAspectRatio = $width / $height;

        if ($oldAspectRatio > $newAspectRatio) {
            // original image is wider than the desired dimensions
            $imageFactory->resize(null, $height);
            $crop = ($imageFactory->getOriginalWidth() - $width) / 2;
            $imageFactory->crop(0, $crop, $crop, 0);
        } else {
            // it's taller...
            $imageFactory->resize($width, null);
            $crop = ($imageFactory->getOriginalHeight() - $height) / 2;
            $imageFactory->crop($crop, 0, 0, $crop);
        }

        $imageFactory->save($imageResized);

    }

    return $this->_storeManager
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path . $image;
}
}
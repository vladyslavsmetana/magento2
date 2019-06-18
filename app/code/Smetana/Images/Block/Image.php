<?php
namespace Smetana\Images\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Smetana\Images\Model\Image\Resize;

/**
 * Block to display image on frontend product page
 */
class Image extends Template
{
    /**
     * Scope Config Interface
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Image Resize Model
     *
     * @var Resize
     */
    private $resize;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Resize $resize
     * @param Context $context
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Resize $resize,
        Context $context
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->resize = $resize;
        parent::__construct($context);
    }

    /**
     * Return config value
     *
     * @param string $option
     *
     * @return string
     */
    private function getConfig(string $option): string
    {
        $value = $this->scopeConfig->getValue(
            "smetana_section/smetana_group/$option",
            ScopeInterface::SCOPE_STORE
        );
        return $value === null ? '' : $value;
    }

    /**
     * Return image path
     *
     * @return string
     */
    public function getImage(): string
    {
        $image = $this->getConfig('smetana_upload_image');
        if ($image == '') {
            return '';
        }
        $path = $this->resize->resize(
            $image,
            (int) $this->getConfig('image_width'),
            (int) $this->getConfig('image_height')
        );

        return $path == '' ? $path : $this->_urlBuilder
                ->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . $path;
    }
}

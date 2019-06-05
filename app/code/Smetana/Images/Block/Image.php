<?php
namespace Smetana\Images\Block;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Smetana\Images\Model\Frontend\Resize;

/**
 * Returning complete image
 */
class Image extends \Magento\Framework\View\Element\Template
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
     * Getting config value
     *
     * @param string $option
     *
     * @return string
     */
    public function getConfig(string $option): string
    {
        $value = $this->scopeConfig->getValue(
            "smetana_section/smetana_group/$option",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $value === null ? '' : $value;
    }

    /**
     * Getting image path
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
                ->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;
    }
}

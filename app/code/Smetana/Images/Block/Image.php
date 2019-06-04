<?php
namespace Smetana\Images\Block;

use \Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Returning complete image
 */
class Image extends \Magento\Framework\View\Element\Template
{
    /**
     * Scope Config Interface
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * Image Resize Model
     *
     * @var \Smetana\Images\Model\Frontend\Resize
     */
    public $resize;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Smetana\Images\Model\Frontend\Resize $resize
     * @param \Magento\Framework\View\Element\Template\Context $context
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Smetana\Images\Model\Frontend\Resize $resize,
        \Magento\Framework\View\Element\Template\Context $context
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
     * @return string or boolean
     */
    public function getImage()
    {
        $image = $this->getConfig('smetana_upload_image');
        if ($image === null || $image == '') {
            return false;
        }
        $path = $this->resize->resize(
            $image,
            (int) $this->getConfig('image_width'),
            (int) $this->getConfig('image_height')
        );
        return $path == false ? '' : substr($path, strpos($path, 'pub'));
    }
}

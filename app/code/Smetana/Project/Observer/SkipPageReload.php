<?php
namespace Smetana\Project\Observer;

use Magento\Framework\Event;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Response\Http;
use Smetana\Project\Block\Options;
use Smetana\Project\Helper\Data as HelperData;

/**
 * SkipPageReload Observer Class
 *
 * @package Smetana\Project\Observer
 */
class SkipPageReload implements Event\ObserverInterface
{
    /**
     * Http Response instance
     *
     * @var Http
     */
    private $response;

    /**
     * Url Builder instance
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Helper Data instance
     *
     * @var HelperData
     */
    private $helperData;

    /**
     * @param Http $response
     * @param UrlInterface $urlBuilder
     * @param HelperData $helperData
     */
    public function __construct(
        Http $response,
        UrlInterface $urlBuilder,
        HelperData $helperData
    ) {
        $this->response = $response;
        $this->urlBuilder = $urlBuilder;
        $this->helperData = $helperData;
    }

    /**
     * Skip order page reload after waiting for order
     *
     * @param Event\Observer $observer
     *
     * @return SkipPageReload
     */
    public function execute(Event\Observer $observer): SkipPageReload
    {
        if (
            $this->helperData->isButtonDisabled()
            && $observer->getData('skip')
        ) {
            $this->response
                ->setRedirect($this->urlBuilder->getUrl(Options::PATH_TO_ORDER_GRID))
                ->sendResponse();
        }

        return $this;
    }
}

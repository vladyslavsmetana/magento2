<?php
namespace Smetana\Project\Ui;

use Magento\Framework\UrlInterface;
use Magento\Ui\Component\MassAction as UiMassAction;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Smetana\Project\Helper\Data as HelperData;
use Smetana\Project\Block\Options;

/**
 * Class MassAction Ui Component
 *
 * @package Smetana\Project\Ui
 */
class MassAction extends UiMassAction
{
    /**
     * Url Builder instance
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $components, $data);
    }

    /**
     * Change MassAction configuration
     *
     * @param void
     *
     * @return void
     */
    public function prepare(): void
    {
        parent::prepare();

        if (HelperData::isCoordinator()) {
            $data = [
                'component' => 'uiComponent',
                'label' => __('Clean initiator'),
                'type' => 'clean_initiator',
                'url' => $this->urlBuilder->getUrl(Options::PATH_TO_REMOVE_INITIATOR),
            ];

            $config = $this->getConfiguration();
            $config['actions'][] = $data;

            $this->setData('config', $config);
        }
    }
}

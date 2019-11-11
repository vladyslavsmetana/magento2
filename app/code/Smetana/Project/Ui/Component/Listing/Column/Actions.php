<?php
namespace Smetana\Project\Ui\Component\Listing\Column;

use Smetana\Project\Block\Options;
use Smetana\Project\Helper\Data as HelperData;
use Magento\Sales\Ui\Component\Listing\Column\ViewAction;

/**
 * Class configure Order grid
 *
 * @package Smetana\Project\Ui\Component\Listing\Column
 */
class Actions extends ViewAction
{
    /**
     * Add clean action to Order grid column
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        $dataSource = parent::prepareDataSource($dataSource);
        if (
            HelperData::isCoordinator()
            && isset($dataSource['data']['items'])
        ) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['clean_initiator'] = [
                    'href' => $this->urlBuilder->getUrl(
                        Options::PATH_TO_REMOVE_INITIATOR,
                        ['order_id' => $item['entity_id']]
                    ),
                    'label' => __('Clean initiator'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}

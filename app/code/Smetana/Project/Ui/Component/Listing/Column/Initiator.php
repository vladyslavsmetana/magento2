<?php
namespace Smetana\Project\Ui\Component\Listing\Column;

use Magento\User\Model\ResourceModel\User;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Initiator column options class
 *
 * @package Smetana\Project\Ui\Component\Listing\Column
 */
class Initiator implements OptionSourceInterface
{
    /**
     * Initiator column options
     *
     * @var array
     */
    private $options;

    /**
     * User Collection factory instance
     *
     * @var User\CollectionFactory
     */
    private $userCollectionFactory;

    /**
     * @param User\CollectionFactory $userCollectionFactory
     */
    public function __construct(
        User\CollectionFactory $userCollectionFactory
    ) {
        $this->userCollectionFactory = $userCollectionFactory;
    }

    /**
     * Change displayed initiator column data
     *
     * @param void
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if (is_null($this->options)) {
            /** @var User\Collection $collection */
            $collection = $this->userCollectionFactory->create();

            foreach ($collection as $user) {
                $this->options[] = [
                    'value' => $user->getData('user_id'),
                    'label' => $user->getData('username'),
                ];
            }
        }

        return $this->options;
    }
}

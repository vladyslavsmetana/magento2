<?php
namespace Vlatame\Customers\Plugin;

use Vlatame\Customers\Block\AttributeNames;
use Magento\Customer\Model\Session;
use Magento\Directory\Model\ResourceModel\Country\Collection;

/**
 * Override Country data according to customer attributes
 */
class CustomerCountryProvider
{
    /**
     * Frontend customer session instance
     *
     * @var Session
     */
    private $customerSession;

    /**
     * @param Session $customerSession
     */
    public function __construct(
        Session $customerSession
    )
    {
        $this->customerSession = $customerSession;
    }

    /**
     * Change Country data
     *
     * @param Collection $pluginObject
     *
     * @return Collection
     */
    public function afterLoadByStore(Collection $pluginObject): Collection
    {
        $customerData = $this->customerSession->getCustomer()->getData();
        if (
            !empty($customerData)
            && isset($customerData[AttributeNames::RESTRICTION_ENABLE])
            && $customerData[AttributeNames::RESTRICTION_ENABLE]
        ) {
            $allowedCountries = isset($customerData[AttributeNames::COUNTRIES_RESTRICTION])
                ? $customerData[AttributeNames::COUNTRIES_RESTRICTION]
                : [];
            $pluginObject->addFieldToFilter("country_id", ['in' => $allowedCountries]);
        }
        return $pluginObject;
    }
}

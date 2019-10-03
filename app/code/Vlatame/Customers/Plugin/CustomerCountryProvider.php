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
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * Change Country data
     *
     * @param Collection $pluginObject
     * @param array $options
     *
     * @return array
     */
    public function afterToOptionArray(Collection $pluginObject, array $options): array
    {
        $customerData = $this->customerSession->getCustomer()->getData();
        if (
            !empty($customerData)
            && $customerData[AttributeNames::RESTRICTION_ENABLE]
            && isset($customerData[AttributeNames::COUNTRIES_RESTRICTION])
        ) {
            $restrictCountries = explode(',', $customerData[AttributeNames::COUNTRIES_RESTRICTION]);
            $countryList = array_column($options, 'value');
            foreach ($restrictCountries as $country) {
                if (in_array($country, $countryList)) {
                    unset($options[array_search($country, $countryList)]);
                }
            }
        }

        return $options;
    }
}

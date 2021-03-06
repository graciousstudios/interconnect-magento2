<?php
namespace Gracious\Interconnect\Http\Request\Data;

use Gracious\Interconnect\Support\EntityType;
use Magento\Customer\Model\Data\Address as AddressModel;


class Address extends Data
{

    /**
     * @param AddressModel|\Magento\Sales\Api\Data\OrderAddressInterface|\Magento\Customer\Api\Data\AddressInterface $address
     * @return array
     */
    public function setupData($address) {
        $street = $address->getStreet();
        $street = is_array($street) ? implode(' ', $street) : $street;

        return [
            'street'        => $street,
            'zipcode'       => $address->getPostcode(),
            'city'          => $address->getCity(),
            'country'       => $address->getCountryId(),
            'company'       => $address->getCompany(),
            'telephone'     => $address->getTelephone()
        ];
    }
}
<?php
namespace Gracious\Interconnect\Http\Request\Data\Address;

use Gracious\Interconnect\Http\Request\Data\FactoryAbstract;
use Gracious\Interconnect\Support\EntityType;
use Magento\Customer\Model\Data\Address;


class Factory extends FactoryAbstract
{

    /**
     * @param Address|\Magento\Sales\Api\Data\OrderAddressInterface|\Magento\Customer\Api\Data\AddressInterface $address
     * @return array
     */
    public function setupData($address) {
        $addressId = $address->getId();
        $uniqueId = $addressId !== null ? $this->generateEntityId($address->getId(), EntityType::ADDRESS) : null;
        $street = $address->getStreet();
        $street = is_array($street) ? implode(' ', $street) : $street;

        return [
            'addressId'     => $uniqueId,
            'street'        => $street,
            'zipcode'       => $address->getPostcode(),
            'city'          => $address->getCity(),
            'country'       => $address->getCountryId(),
            'company'       => $address->getCompany(),
            'telephone' => $address->getTelephone()
        ];
    }
}
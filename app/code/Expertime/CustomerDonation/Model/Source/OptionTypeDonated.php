<?php

/**
 * @author Jo Ng <ngkwokwah@gmail.com>
 */

namespace Expertime\CustomerDonation\Model\Source;

class OptionTypeDonated implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $typesOfStatus = [
            0 => 'No',
            1 => 'Yes'
        ];
        $options = [];
        foreach ($typesOfStatus as $value) {
            $options[] = [
                'label' => $value,
                'value' => $value
            ];
        }

        return $options;
    }
}

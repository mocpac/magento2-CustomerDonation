<?php

/**
 * @author Jo Ng <ngkwokwah@gmail.com>
 */

namespace Expertime\CustomerDonation\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Config\Block\System\Config\Form\Field;

/**
 * WYSIWYG Editor of donation description
 */
class Editor extends Field
{
    public function __construct(
        Context $context,
        WysiwygConfig $wysiwygConfig,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setWysiwyg(true);
        $config = $this->wysiwygConfig->getConfig($element);
        $config->setplugins([]);
        $config->setadd_variables(0);
        $config->setadd_widgets(0);
        $element->setConfig($config);

        return parent::_getElementHtml($element);
    }
}

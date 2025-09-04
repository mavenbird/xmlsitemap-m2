<?php 

namespace Mavenbird\XmlSitemap\Block\Adminhtml\Render;

use Magento\Config\Block\System\Config\Form\Field;
use Mavenbird\Core\Helper\Data as CoreHelper;


class Header extends Field
{
    protected $_template = 'Mavenbird_XmlSitemap::system/config/header.phtml';

    protected CoreHelper $coreHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        CoreHelper $coreHelper,
        array $data = []
    ) {
        $this->coreHelper = $coreHelper;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getDeveloperMessage(): string
    {
        return $this->coreHelper->getDeveloperMessage();
    }
}

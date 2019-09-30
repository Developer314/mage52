<?php
namespace Revolve\Mage25\Model\ResourceModel\Block;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define model & resource model
     */
     protected $_idFieldName = 'btID';
    protected function _construct()
    {
    $this->_init(
        'Revolve\Mage25\Model\Block',
        'Revolve\Mage25\Model\ResourceModel\Block'
    );

    }
    
    
    
    
    
    
}
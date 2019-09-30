<?php
namespace Revolve\Mage25\Model\ResourceModel\Area;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define model & resource model
     */
     protected $_idFieldName = 'arID';
    protected function _construct()
    {
    $this->_init(
        'Revolve\Mage25\Model\Area',
        'Revolve\Mage25\Model\ResourceModel\Area'
    );

    }
    
    
    
    
    
    
}
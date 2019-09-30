<?php
namespace Revolve\Mage25\Model\ResourceModel\Pages;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define model & resource model
     */
     protected $_idFieldName = 'cID';
    protected function _construct()
    {
    $this->_init(
        'Revolve\Mage25\Model\Pages',
        'Revolve\Mage25\Model\ResourceModel\Pages'
    );

    }
    
    
    
    
    
    
}
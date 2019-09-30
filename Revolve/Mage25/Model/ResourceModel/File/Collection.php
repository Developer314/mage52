<?php
namespace Revolve\Mage25\Model\ResourceModel\File;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define model & resource model
     */
     protected $_idFieldName = 'fID';
    protected function _construct()
    {
    $this->_init(
        'Revolve\Mage25\Model\File',
        'Revolve\Mage25\Model\ResourceModel\File'
    );

    }
    
    
    
    
    
    
}
<?php
namespace Revolve\Mage25\Model\ResourceModel;
class Area extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     * https://www.ashsmith.io/magento2/module-from-scratch-module-part-2-models/
     */
    
    
    
    
    protected function _construct()
    {
    	$this->_init('mage25_areas', 'arID');   //here id is the primary key of custom table
    	
    	
    }
    
    
    public function getAreaByHandle($arHandle)
    {
        $adapter = $this->getConnection();
        
        
        $select  = $adapter->select()
            ->from($this->getMainTable(), '*')
            ->where('arHandle = :arHandle');
        $binds   = ['arHandle' => $arHandle];
		
		
		
        return $adapter->fetchRow($select, $binds);
        
        
    }
    
    public function createArea($arHandle){
	    $data = ['arHandle'=>$arHandle];
	    $adapter = $this->getConnection();
		$data = $adapter->insert($this->getMainTable(), $data);
	    return $this->getAreaByHandle($arHandle);
    }
    
    
    
    
}
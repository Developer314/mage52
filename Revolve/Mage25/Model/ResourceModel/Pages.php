<?php
namespace Revolve\Mage25\Model\ResourceModel;
class Pages extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     * https://www.ashsmith.io/magento2/module-from-scratch-module-part-2-models/
     */
    
    
    
    
    protected function _construct()
    {
    	$this->_init('mage25_pages', 'cID');   //here id is the primary key of custom table
    	
    	
    }
    
    
    public function findPages($title){
	    $adapter = $this->getConnection();
       
        $select  = $adapter->select()
            ->from('cms_page', '*');
           // ->where('title LIKE \'%:title%\'');
            
            $binds   = ['title' => $title];
       
		return $adapter->fetchAll($select, $binds);
    }
    
     
    
    
}
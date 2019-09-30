<?php
namespace Revolve\Mage25\Block\Productlist;


class Controller extends \Revolve\Mage25\Block\Bootstrap
{
		public  $btTable = 'mage25_btproductlist';
		public  $btInterfaceWidth = "600px";
		public  $btInterfaceHeight = "auto";
		
		
		public  function getBlockTypeDescription() {
			return "Magento2 Product List";
		}
		
		public  function getBlockTypeName() {
			return "Product List";
		}
		
		public  function installDB(){
			
				
				return 'CREATE TABLE IF NOT EXISTS `mage25_btproductlist` (
  `bID` int(255) NOT NULL AUTO_INCREMENT,
  `heading` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_id` varchar(255) DEFAULT NULL,
  `attributes` text COLLATE utf8_unicode_ci,
  `per_page` int(255) DEFAULT NULL,
  PRIMARY KEY (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;';
				
				
				
				
				
		}
		
		public  function updateDB(){
			return '';
		}
		
		public  function upgrade(){
			$this->updateDB();
		}
		
		public  function add($data){
			parent::add($data);
		}
		
		public  function save($data){
			
			
			$attributes = array();
			foreach($data['data'] as $k=>$v){
			
				if(preg_match('/mage25_filter_/', $k)){
				
					$newval = str_replace('mage25_filter_','', $k);
					$attributes[$newval] = $v;
					unset($data['data'][$k]);
				}
			}
			
			$data['data']['attributes'] = serialize($attributes);
			
			return parent::save($data);
			
		}
		
		public function view($blockData){
			
			parent::view($blockData);
			
			
		}
		
		public  function getSearchableAttributes(){
			
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			
			$field_helper = $objectManager->get('Magento\CatalogSearch\Model\Advanced');
			return $field_helper->getAttributes();
			
		}
		
		
		
		public  function getAttributeInputType($attribute)
	    {
		    $allowedFields = array("text", 'price', 'boolean','multiselect','select');
	      //  $dataType   = $attribute->getBackend()->getType();
	        $inputType  = $attribute->getFrontend()->getInputType();
	        if(in_array($inputType, $allowedFields)){
		        return $inputType;
	        }else{
		        return false;
	        }
		   
		   
	    }
	    
	    public  function getAttributeLabel($attribute){
		    return $attribute->getStoreLabel();
	    }
		
		public  function displayPagePicker($field, $cID=0){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			
			$field_helper = $objectManager->get('\Revolve\Mage25\Helper\Field');
			return $field_helper->displayPagePicker($field, $cID);
		}
		
		public  function getAttributeSelectElement($attribute)
		{
	        //$extra = '';
	        $options = $attribute->getSource()->getAllOptions(false);
	        
	        
	        return $options;

    		}
    
    
    
	     public  function getAttributeYesNoElement($attribute)
	    {
	        return $options = array(
	            array('value' => '',  'label' => __('All')),
	            array('value' => '1', 'label' => __('Yes')),
	            array('value' => '0', 'label' => __('No'))
	        );
	
		}	
	
	
		public  function getCurrency()
	    {
		    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		    $field_helper = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		    return $field_helper->getStore()->getCurrentCurrencyCode();
	
	    }
	    
	    
	    public function getProductCollection($data){
		    
		    //https://mage2.pro/t/topic/1248
		    
		    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		    $url_helper = $objectManager->get('Revolve\Mage25\Helper\Url');
		    if($data['category'] == 'custom'){
			     $category_id = $url_helper->getID($data['category_id']);
			     
		    }elseif($data['category'] == 'current'){
			    $category_id = $this->getCurrentCategory();
		    }else{
			    $category_id = 2;
		    }
		    
		    $products = $this->getCategoryProducts($category_id);
		    $products->addFinalPrice();
		    
		    $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
		    $products->setCurPage($page);
		    
		    $attributes = unserialize($data['attributes']);
		    
		    
		
		    foreach($attributes as $attr=>$v){
			    
			   $flds = explode('_', $attr);
			   $fieldType = end($flds);
			   array_pop($flds);
			   $attr = implode('_', $flds);
			    //echo $fieldType."<br />";
			    if(!$v) continue;
			    switch($fieldType){
				    case "price":
				    		if(is_array($v)){
						   $fromPrice = $v[0];
						   $toPrice = $v[1];
						   if($fromPrice > 0 and $toPrice > $fromPrice){
							   $products->getSelect()->where("price_index.final_price > ".$fromPrice)->where("price_index.final_price < ".$toPrice);
						   }elseif($fromPrice > 0){
							   $products->getSelect()->where("price_index.final_price > ".$fromPrice);
						   }elseif($toPrice>0){
							    $products->getSelect()->where("price_index.final_price < ".$toPrice);
						   }
						}
				    break;
				    
				    case "text":
				    		$products->addAttributeToFilter($attr, ['like' => "%".$v."%"] );
				    break;
				    
				    case "boolean":
				    		if($v >0){
					    		//echo $attr;
				    			$products->addAttributeToFilter($attr, '1');
				    		}
				    break;
				    
				    case "multiselect":
					    	if(is_array($v)){
					    		$products->addAttributeToFilter($attr, ['in' => $v]);
					    	}
				    break;
				    
				    case "select":
				    		if($v >0){
				    			$products->addAttributeToFilter($attr, $v);
				    		}
				    break;
				    
				    
			    }
			 }
		   
		   
		    $limit=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : $data['per_page'];
		     $products->setPageSize($limit);
			 return $products;
	    }
	    
	    public function getCategoryProducts($categoryId){
		    
		    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		    $category_helper = $objectManager->get('\Magento\Catalog\Model\CategoryFactory');
		    
		    $products = $category_helper->create()->load($categoryId)->getProductCollection();
		    $products->addAttributeToSelect('*');
		    $products->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
		    $products->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
		    
		    
		    
		    return $products;
		    
	    }
	    
	    public function getCurrentCategory(){
		    
		    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		    $category_helper = $objectManager->get('\Magento\Framework\ObjectManagerInterface');
		    $category = $category_helper->get('Magento\Framework\Registry')->registry('current_category');
		    
		   if(is_object($category)){
		   	 return $category->getId();
		    }else{
			    return 2;
		    }
		    
		    
	    }
	    
	    public function getPager($collection){
		    $pager = $this->getLayout()->createBlock(
		            'Magento\Theme\Block\Html\Pager',
		            'mage25.productlist.pager'
		        )->setAvailableLimit(array(4=>4,8=>8,12=>12,16=>16))->setShowPerPage(true)->setCollection(
		            $collection
		        )->toHtml();
		       return $pager;
	    }
		
	   public function getStoreCurrency(){
		   $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
			$storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
			$currencyCode = $storeManager->getStore()->getCurrentCurrencyCode(); 
			$currency = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($currencyCode); 
			return $currency->getCurrencySymbol(); 
	   }

}
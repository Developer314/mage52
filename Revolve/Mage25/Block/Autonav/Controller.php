<?php
namespace Revolve\Mage25\Block\Autonav;


class Controller extends \Revolve\Mage25\Block\Bootstrap
{
		public  $btTable = 'mage25_btnavigation';
		public  $btInterfaceWidth = "600";
		public  $btInterfaceHeight = "465";
		public $category;
		public $categoryRepository;
		public $associatedTables = array();
		public $urlHelper;
		public $currentCategory;
		public $icon = 'bars';
		/*
		public function __construct(
        \Magento\Catalog\Helper\Category $category,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository
	    ) {
	        $this->category = $category;
	        $this->categoryRepository = $categoryRepository;
	    }*/
		
		public  function getBlockTypeDescription() {
			return __("Magento 2 Category Navigation");
		}
		
		public  function getBlockTypeName() {
			return __("Auto Nav");
		}
		
		public  function installDB(){
			
				
				return "CREATE TABLE IF NOT EXISTS `mage25_btnavigation` (
  `bID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderBy` varchar(255) DEFAULT 'alpha_asc',
  `displayPages` varchar(255) DEFAULT 'top',
  `displayPagesCID` varchar(255) DEFAULT 'none',
  
  `displaySubPages` varchar(255) DEFAULT 'none',
  `displaySubPageLevels` varchar(255) DEFAULT 'none',
 
  PRIMARY KEY (`bID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;";
				
				
				
				
				
		}
		
		public  function updateDB(){
			return '';
		}
		
		
		
		public  function add($data){
			return parent::add($data);
		}
		
		public  function save($data){
			
			return parent::save($data);
		
		}
		
		public function view($blockData){
			
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$category = $objectManager->get('\Magento\Catalog\Helper\Category');
			$this->category = $category;
			
			
			$categoryRepository = $objectManager->get('\Magento\Catalog\Model\CategoryRepository');
			$this->categoryRepository = $categoryRepository;
			
			$urlHelper = $objectManager->get('Revolve\Mage25\Helper\Url');
			$this->urlHelper = $urlHelper;
			
			
		     $currentCategory = $objectManager->get('Magento\Framework\Registry');
		     $this->currentCategory = $currentCategory->registry('current_category');
		     
		     return parent::view($blockData);
			
		}
		
		/*public function generateNav(){
			
			
			$categories = $this->category->getStoreCategories();
			return $categories;
		}*/
		
		public  function displayPagePicker($field, $cID=0){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$field_helper = $objectManager->get('\Revolve\Mage25\Helper\Field');
			return $field_helper->displayPagePicker($field, $cID);
		}
		
		
		public function  displayCategories($categories, $mainClass='', $subClass='') {
			
			
			
		    $array= '<ul class="'.$mainClass.'">';
		    foreach($categories as $category) {
		       
		       
		        
		        $array .= '<li>'.
		        '<a href="'.$category->getUrl().'">' .$category->getName() . "</a>\n";
		        if($category->hasChildren()) {
		            	$children = $this->categoryRepository->get($category->getId());
		            	 $children = $children->getChildrenCategories();
				  	$array .=  $this->displayCategories($children, $subClass);
		            }
		         $array .= '</li>';
		    }
		    return  $array . '</ul>';
		}
		
}
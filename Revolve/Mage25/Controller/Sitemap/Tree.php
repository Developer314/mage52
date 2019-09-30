<?php
namespace Revolve\Mage25\Controller\Sitemap;
use Magento\Framework\App\Action\Context;
use \Magento\Framework\Registry as Registry;
use Revolve\Mage25\Model\Pages as Mage25Pages;
use Revolve\Mage25\Block\Bootstrap as BlockBootstrap;
class Tree extends \Magento\Framework\App\Action\Action
{
	protected $_registry;
	protected $_categoryFactory;
	protected $_pageParent;
	protected $_searchCriteriaBuilder;
	protected $_objectManager;
	protected $_storeConfig;
	 protected $_blockClass;
	public function __construct(Context $context, 
	Registry $registry, 
	\Magento\Catalog\Model\Category $categoryFactory,
	
	\Magento\Framework\App\Config\ScopeConfigInterface $storeConfig,
	Mage25Pages $pageParent, BlockBootstrap $blockBootstrap)
    {
	    parent::__construct($context);
	    $this->_registry = $registry;
	    $this->_categoryFactory = $categoryFactory;
	    $this->_pageParent = $pageParent;
	    $this->_objectManager = $context->getObjectManager();
	    $this->_storeConfig = $storeConfig;
	     $this->_blockClass = $blockBootstrap;
    }

	public function execute()
    {
	     if(!$this->_blockClass->isAdminLoggedIn()) die("Access Denied");
		if($blockAction = $this->_request->getParam('action')){
		    echo $this->$blockAction();
		    die();
	    }

		

    }

    protected function _prepareLayout()
	{

       parent::_prepareLayout();

    }
    
    
    public function sitemap(){
	    

		 $elementid = $this->_request->getParam('elementid');
		 //$elementid = 'testelement';
		 $this->_registry->register('elementid', $elementid);
		 $this->_registry->register('object', $this);
	     echo $this->_view->getLayout()->createBlock("Revolve\Mage25\Block\Bootstrap")->setTemplate("Revolve_Mage25::sitemap.phtml")->toHtml();

	}
	
	public function get_sub_cats($parentCategory = 2){
		
		if($node = $this->_request->getParam('node')){
			$parentCategory = $node;
			
		}
		$cat = $this->_categoryFactory->load($parentCategory);
		$categories = $cat->getChildrenCategories();
		$cats = array();
		
		
		
		
		
		foreach($categories as $cat){
			$loadOnDemad = $cat->hasChildren() ? true : false;
			array_push($cats, array('label'=>$cat->getName(),'id'=>$cat->getId(), 'load_on_demand'=>$loadOnDemad));
		}
		
		
		if($parentCategory == 2){
			$cats = array('label'=>__('Home'), 'id'=>'1','children'=>$cats);
			return '['.json_encode($cats).']';
		}else{
			return json_encode($cats);
		}
		
		
	}
	
	
	
	public function fetch_products($parent=1){
		
		$keyword = $this->_request->getParam('keyword');
		$elementid = $this->_request->getParam('elementid');
		
		$productcollection = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
            ->addAttributeToFilter('name',['like'=>'%'.$keyword.'%']);
		$productcollection->addAttributeToSelect('*');
		
		 $productcollection->setPageSize(50);
		
		if(sizeof($productcollection) > 0 ){
		
			echo '<table class="table">';
		$store = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
		
		
			foreach($productcollection as $product){
				$imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .'catalog/product/'.$product->getData('image');
				//$imageUrl = $product->getData('image');
				
				echo '<tr>';
				
				if($product->getImage()){
					echo '<td>';
					echo '<img src="'.$imageUrl.'" width="100">';
					echo '</td>';
				}else{
					echo '<td>';
					echo '<img src="'.$this->_storeConfig->getValue('catalog/placeholder/image_placeholder') .'" width="100">';
					echo '</td>';
				}
				
				echo '<td>'.$product->getName().'</td>';
				
				if($elementid !=''){
					echo '<td>';
					echo '<button type="button" class="btn-small btn-info" onclick="mage25_selectPageToLink(\'product-'.$product->getID().'\', \''.$product->getName().'\',\''.$elementid.'\')"><span>Choose</span></button>';
					echo '</td>';
				}
				echo '</tr>';
				
			}
			
			echo '</table>';
		
		}
            
       die();     
            
            
            
	}
	
	public function fetch_pages($page = 0){
		
		
		$keyword = $this->_request->getParam('keyword');
		$elementid = $this->_request->getParam('elementid');
		$cmscollection = $this->_pageParent->findPages($keyword);
		
		if(sizeof($cmscollection) > 0 ){
		
			echo '<table class="table">';
			foreach($cmscollection as $page){
				
				if(!preg_match('/'.strtolower($keyword).'/', strtolower($page['title']))) continue;
				
				echo '<td>'.$page['title'].'</td>';
				
				if($elementid !=''){
					echo '<td>';
					echo '<button type="button" class="btn-small btn-info" onclick="mage25_selectPageToLink(\'page-'.$page['page_id'].'\', \''.$page['title'].'\',\''.$elementid.'\')"><span>Choose</span></button>';
					echo '</td>';
				}
				echo '</tr>';
				
			}
			
			echo '</table>';
		
		}
            
       die();
		
		
		
		
		
	}
	
	
	
	
    
    
    
}
<?php
namespace Revolve\Mage25\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_urlInterface;
   	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
		array $data = []
    ){
	    
	    
	    
	     $this->_urlInterface = $context->getUrlBuilder();
    }
    
    public function isClassExists($class){
	   return class_exists($class);
    }
    
    public function getSiteUrl($current = false, $customPage = ''){

         return $this->_urlInterface->getBaseUrl();

    }
    
    
    public function isEditorLoggedIn(){
	    
    }
    
    
}
?>
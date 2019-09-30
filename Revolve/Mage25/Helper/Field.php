<?php
namespace Revolve\Mage25\Helper;
use \Revolve\Mage25\Model\File as RevolveFile;
use Revolve\Mage25\Model\Pages as SitemapPage;
class Field extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_urlInterface;

	protected $_mFile;
	protected $_spage;
   	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
      
         RevolveFile $revolveFile,
         SitemapPage $siteMapPage,
       array $data = []
    ){
	      $this->_urlInterface = $context->getUrlBuilder();
	     $this->_mFile = $revolveFile;
	     $this->_spage = $siteMapPage;
    }
    
    public function displayFilePicker($fieldName, $fID=0){
	    $filePath = '';
	    if($fID>0){
		    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		    $urlHelper = $objectManager->get('\Revolve\Mage25\Helper\Url');
		    $filePath = $urlHelper->getFileUrl($fID, false);
		    $filePath = $urlHelper->getMediaFile($filePath, 75, 0);
		    $textToDisplay = $filePath;
	    }else{
		    $textToDisplay = __("Choose File");
	    }
	    $fieldHtml = '<div class="mage25_file_manager_select_file">';
	    $fieldHtml.='<input type="hidden" value="'.$fID.'" name="'.$fieldName.'" id="mage25_filepicker_'.$fieldName.'_value" />';
	    $fieldHtml.='<div id="mage25_file_selector_click_'.$fieldName.'" onclick="mage25_storeFileInDivInput(\''.$fieldName.'\')">'.$textToDisplay.'</div>';
	    $fieldHtml.="</div>";
	    
	    return $fieldHtml;
	    
    }
    
    public function displayPagePicker($fieldName, $cID = null){
	    
	    $textToDisplay = __('Choose Page');
	    if($cID){
		    $textToDisplay = $this->_spage->getPageName($cID);
	    }
	    
	    $fieldHtml = '<div class="mage25_sitemap_select_page">';
	    $fieldHtml.='<input type="hidden" value="'.$cID.'" name="'.$fieldName.'" id="mage25_sitemap_page_'.$fieldName.'_value" />';
	     $fieldHtml.='<div id="mage25_sitemap_page_click_'.$fieldName.'" onclick="mage25_storePageInDivInput(\''.$fieldName.'\')">'.$textToDisplay.'</div>';
	    $fieldHtml.="</div>";
	    return $fieldHtml;
    }
    

   
   
   
}
?>
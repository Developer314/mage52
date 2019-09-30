<?php
namespace Revolve\Mage25\Block\File;


class Controller extends \Revolve\Mage25\Block\Bootstrap
{
		public  $btTable = 'mage25_btfile';
		public  $btInterfaceWidth = "600px";
		public  $btInterfaceHeight = "auto";
		
		
		
		public  function getBlockTypeDescription() {
			return __("Link to files stored in the asset library.");
		}
		
		public  function getBlockTypeName() {
			return __("File");
		}
		
		public  function installDB(){
			return 'CREATE TABLE IF NOT EXISTS `mage25_btfile` (
  `bID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fID` int(10) NOT NULL,
  `downloadText` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `target` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`bID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;';
		}
		
		public  function updateDB(){
			return '';
		}
		
		public  function upgrade(){
			$this->updateDB();
		}
	
	public  function add($data){
			return parent::add($data);
		}
		
		public  function save($data){
	
			return parent::save($data);
		}
		
		public function view($blockData){
	
			return parent::view($blockData);

		}	
		public  function mediaPath($fID){
		  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		    $urlHelper = $objectManager->get('\Revolve\Mage25\Helper\Url');
		    $filePath = $urlHelper->getFileUrl($fID);
			return $filePath;
	
		}
	public  function displayFilePicker($field, $fID=0){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			
			$field_helper = $objectManager->get('\Revolve\Mage25\Helper\Field');
			return $field_helper->displayFilePicker($field, $fID);
		}	

}
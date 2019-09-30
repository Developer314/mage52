<?php
namespace Revolve\Mage25\Block\Content;


class Controller extends \Revolve\Mage25\Block\Bootstrap
{
		public  $btTable = 'mage25_btcontent';
		public  $btInterfaceWidth = "600";
		public  $btInterfaceHeight = "465";
		public  $data = array();
		
		
				
		public  function getBlockTypeDescription() {
			return __("Content/WSYSIG Data");
		}
		
		public  function getBlockTypeName() {
			return __("Content");
		}
		
		public  function installDB(){
			return 'CREATE TABLE IF NOT EXISTS `mage25_btcontent` (
				  `bID` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `content` longtext,
				  PRIMARY KEY (`bID`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;';
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
			
			return parent::save($data);
			
		}
		
		public  function view($blockData){
			
			//$object_manager = \Magento\Core\Model\ObjectManager::getInstance();
			/*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			
			$url_helper = $objectManager->get('\Revolve\Mage25\Helper\Url');
			
			$blockData['blockData']['content'] = $url_helper->translateToView($blockData['blockTypeData']['content']);
			*/
			return parent::view($blockData);
		
		}
		
		public function translateToView($content){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$url_helper = $objectManager->get('\Revolve\Mage25\Helper\Url');
			return $url_helper->translateToView($content);
			
		}
		
		
		
		
		public  function getDataName(){
			return "Sample data";
		}
		
		public  function edit($data){
			parent::edit($data);
		}
		
}
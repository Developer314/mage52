<?php
namespace Revolve\Mage25\Block\Staticblock;


class Controller extends \Revolve\Mage25\Block\Bootstrap
{
		public  $btTable = 'mage25_btstaticblocks';
		public  $btInterfaceWidth = "400px";
		public  $btInterfaceHeight = "auto";
		public  $data = array();
		
		
				
		public  function getBlockTypeDescription() {
			return __("Displays Magento 2 Static Blocks");
		}
		
		public  function getBlockTypeName() {
			return __("Static Blocks");
		}
		
		public  function installDB(){
			return 'CREATE TABLE IF NOT EXISTS `mage25_btstaticblocks` (
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
			
			
			
			
			$data['data']['content'] = implode(',', $data['data']['static_block']);
			unset($data['data']['static_block']);
			return parent::save($data);
			
			
		}
		
		public  function view($blockData){
			
			
			return parent::view($blockData);
		
		}
		
		
		
		public  function edit($data){
			parent::edit($data);
		}
		
		
		
		public function getStaticBlocks(){
			
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			
			$field_helper = $objectManager->get('\Magento\Cms\Model\BlockFactory');
			return $field_helper->create()->getCollection()->load();
		}
		
		public function getBlockByID($bID){
			$blocks = $this->getStaticBlocks();
			foreach($blocks as $_block){
				if($_block->getIdentifier() == $bID) return $_block;
			}
		}
		
}
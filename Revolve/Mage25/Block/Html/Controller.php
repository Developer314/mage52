<?php
namespace Revolve\Mage25\Block\Html;


class Controller extends \Revolve\Mage25\Block\Bootstrap
{
		public  $btTable = 'mage25_bthtml';
		public  $btInterfaceWidth = "600";
		public  $btInterfaceHeight = "465";
		public  $data = array();
		
		
				
		public  function getBlockTypeDescription() {
			return __("HTML/Embed Content");
		}
		
		public  function getBlockTypeName() {
			return __("HTML");
		}
		
		public  function installDB(){
			return 'CREATE TABLE IF NOT EXISTS `mage25_bthtml` (
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
			
			
			return parent::view($blockData);
			
			
		}
		
		
		
		public  function edit($data){
			
			parent::edit($data);
		}
		
}
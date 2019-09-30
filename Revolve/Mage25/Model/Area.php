<?php
namespace Revolve\Mage25\Model;
use Magento\Framework\Model\AbstractModel;

class Area extends AbstractModel
{
    /**
     * Define resource model
     */
    
    
   protected $areaCollectionFactory;
   
   public $arHandle;
   public $showControls = false;
   public $maximumBlocks = -1;
   public $areaID = 0; 
   public $_mBlock;
   
   public function __construct(
   \Revolve\Mage25\Model\Block $blockFactory,
   \Revolve\Mage25\Model\ResourceModel\Area\CollectionFactory $areaCollectionFactory,
   \Magento\Framework\Model\Context $context,
   \Magento\Framework\Registry $registry,
   \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
   \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
   
   array $data = []
   ){
	   $this->areaCollectionFactory = $areaCollectionFactory;
	   $this->_mBlock = $blockFactory;
	   parent::__construct($context, $registry, $resource, $resourceCollection, $data);
	   
   }
    
    protected function _construct()
    {
		$this->_init('Revolve\Mage25\Model\ResourceModel\Area');
	}
	
	
	public function display($arHandle, $editMode = false){
		
		$this->arHandle = $arHandle;
		$area = $this->getOrCreateArea($arHandle);
		
		$this->areaID = $area['arID'];
		if($editMode){
			$this->showControls = true;
		}else{
			$this->showControls = false;
		}
		
		$this->showBlockViews($this->areaID, $arHandle);
		$this->showAddToAreaDiv($this->areaID, $arHandle);
		
	}
	
	public function getOrCreateArea($arHandle){
		
		if($area = $this->getResource()->getAreaByHandle($arHandle)){
			
			return $area;
		}
		
		return $this->getResource()->createArea($arHandle);
	
	}
	
	public function showBlockViews($areaID, $arHandle){
		$Blocks = $this->_mBlock->getBlocksInThisArea($areaID);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		
		
		
		
		if($this->showControls){
		 	//DragDrop Handle
		 	
		 	echo '<div id="a'.$areaID.'" handle="'.$arHandle.'" class="mage25-area  mage25-area-move-enabled">';
		 	}
		
		foreach($Blocks as $b){
			
			$blockData = $this->_mBlock->getBlockDataBybIDAndBtID($b['bID'],$b['btID']);
			
			$blockData['area_block'] = $b;
			
			
			$class = $blockData['blockType']['btHandle'];
			if(class_exists($class)){
			if($this->showControls){
				
			 echo '<div id="b'.$b['bID'].'-'.$areaID.'-'.$b['btID'].'" btid="'.$b['btID'].'" class="mage25-block" onclick="mage25_showBlockMenu(\''.$b['bID'].'\', \''.$b['btID'].'\', \''.$areaID.'\', \''.$blockData['blockType']['btName'].'\', \''.$blockData['blockType']['btInterfaceWidth'].'\', \''.$blockData['blockType']['btInterfaceHeight'].'\' )">';
			 
		 }
			
			
				//echo $class."<br />";
				$field_helper = $objectManager->get($class);
				$field_helper->view($blockData);
			
			
			
			if($this->showControls){ echo "</div>";  }
			
			
			}
			
			
			
		}
		
		
		if($this->showControls){ echo "</div>"; }
		
		
	}
	
	function showAddToAreaDiv($areaID, $arHandle)
	{
			if($this->showControls){
				
				
				echo '<div onclick="mage25_showAreaMenu(\''.$areaID.'\', \''.$arHandle.'\')" id="a'.$areaID.'controls" class="mage25-add-block">'.$arHandle.'</div>';
				
				
				
			}
	}
	

	
    
    
    
}

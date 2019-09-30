<?php

namespace Revolve\Mage25\Controller\Index;

use Magento\Framework\App\Action\Context;
use Revolve\Mage25\Block\Bootstrap as BlockBootstrap;
class Index extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
     protected $_blockClass;
	
    public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, BlockBootstrap $blockBootstrap)
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_blockClass = $blockBootstrap;
        parent::__construct($context);
    }

    public function execute()
    {
	  if(!$this->_blockClass->isAdminLoggedIn()) die("Access Denied");
	    if($blockAction = $this->_request->getParam('action')){
		      echo $this->$blockAction();
		      die();
	    }
        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }
    
    public function change_edit_exit_mode(){
	    $this->_view->getLayout()->createBlock("Revolve\Mage25\Block\Bootstrap")->isEditMode();
	    die("Success");
    }
    
    
    
}

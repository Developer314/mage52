<?php

namespace Revolve\Mage25\Controller\Blocks;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;

    public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
	    die("here we will list the blocks for adding into the Area");
	    /*
        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
        */
    }
    
    
}

<?php
namespace Revolve\Mage25\Block;


class Filemanager extends \Magento\Framework\View\Element\Template
{
      protected $_fileFactory;

     // use  as RevolveFile;


      public function __construct(
       \Magento\Framework\View\Element\Template\Context $context,
       \Revolve\Mage25\Model\FileFactory $fileFactory,
       array $data = []
    ) {
       $this->_fileFactory = $fileFactory;
       parent::__construct($context, $data);
       //get collection of data
       $collection = $this->_fileFactory->create()->getCollection();
       $this->setCollection($collection);
       //$this->pageConfig->getTitle()->set(__('My Grid List'));
   }

   protected function _prepareLayout()
   {
       parent::_prepareLayout();
       if ($this->getCollection()) {
           // create pager block for collection
           $pager = $this->getLayout()->createBlock(
               'Magento\Theme\Block\Html\Pager',
               'mage25.grid.record.pager'
           )->setCollection(
               $this->getCollection() // assign collection to pager
           );
           $this->setChild('pager', $pager);// set pager block in layout
       }
       return $this;
   }

   /**
    * @return string
    */
   // method for get pager html
   public function getPagerHtml()
   {
       return $this->getChildHtml('pager');
   }
   
   




}

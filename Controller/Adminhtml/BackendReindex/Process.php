<?php

namespace AriyaInfoTech\BackendReindexer\Controller\Adminhtml\BackendReindexer;

/**
 * Class Process
 * @package AriyaInfoTech\BackendReindexer\Controller\Adminhtml\BackendReindexer
 */
class Process extends \Magento\Backend\App\Action
{
    /**
     * Process constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Indexer\Model\IndexerFactory $indexerFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory
    ) {
        parent::__construct($context);
        $this->indexerFactory = $indexerFactory;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $indexerIds = $this->getRequest()->getParam('indexer_ids');
        if (!is_array($indexerIds)) {
            $this->messageManager->addErrorMessage(__('Please select indexers.'));
        } else {
            try {
                foreach ($indexerIds as $indexerId) {
                    $indexer = $this->indexerFactory->create();
                    $indexer->load($indexerId)->reindexAll();
                }
                $this->messageManager->addSuccess(
                    __('%1 item(s) have been reindexed.', count($indexerIds))
                );
            } catch (\Exception $e) {
                $this->getMessageManager()->addErrorMessage($e->getMessage());
            }
        }
        /** @var \Magento\Framework\Controller\Result\Redirect $result */
        $result = $this->resultRedirectFactory->create();
        return $result->setRefererUrl();

    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Indexer::changeMode');
    }
}

<?php
namespace Gw\AdminReindex\Controller\Adminhtml\Indexer;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Indexer\Model\IndexerFactory;
use Throwable;

/**
 * Class ReindexOnTheFly
 */
class Reindex extends Action implements HttpPostActionInterface
{

    /** @var IndexerInterface */
    private $indexerFactory;

    /**
     * ReindexOnTheFly constructor.
     * @param Context $context
     * @param IndexerFactory $indexerFactory
     */
    public function __construct(
        Context $context,
        IndexerFactory $indexerFactory
    ) {
        $this->indexerFactory = $indexerFactory;
        parent::__construct($context);
    }

    /**
     * @return void
     * @throws Throwable
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

                $this->messageManager->addSuccessMessage(
                    __('Reindex %1 indexer(s).', count($indexerIds))
                );
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __("We couldn't reindex because of an error.")
                );
            }
        }

        $this->_redirect('indexer/indexer/list');
    }
}

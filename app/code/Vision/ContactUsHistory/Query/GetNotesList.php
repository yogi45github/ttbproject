<?php
/**
 * @author Vitaliy Boyko <vision@i.ua>
 */
declare(strict_types=1);

namespace Vision\ContactUsHistory\Query;

use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Vision\ContactUsHistory\Api\Query\GetNotesListInterface;
use Vision\ContactUsHistory\Mapper\NotesDataMapper;
use Vision\ContactUsHistory\Model\ResourceModel\Note\NoteCollection;
use Vision\ContactUsHistory\Model\ResourceModel\Note\NoteCollectionFactory;

/**
 * @inheritdoc
 */
class GetNotesList implements GetNotesListInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var NoteCollectionFactory
     */
    private $noteCollectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var NotesDataMapper
     */
    private $noteDataMapper;

    /**
     * @param CollectionProcessorInterface $collectionProcessor
     * @param NoteCollectionFactory $noteCollectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param NotesDataMapper $noteDataMapper
     */
    public function __construct(
        CollectionProcessorInterface $collectionProcessor,
        NoteCollectionFactory $noteCollectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        NotesDataMapper $noteDataMapper
    ) {
        $this->collectionProcessor = $collectionProcessor;
        $this->noteDataMapper = $noteDataMapper;
        $this->noteCollectionFactory = $noteCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var NoteCollection $collection */
        $collection = $this->noteCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var SearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $noteDataObjects = $this->noteDataMapper->map($collection);

        $searchResult->setItems($noteDataObjects);
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;
    }
}

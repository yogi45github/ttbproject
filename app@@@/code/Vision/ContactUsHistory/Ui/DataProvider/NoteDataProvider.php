<?php
/**
 * @author Vitaliy Boyko <vision@i.ua>
 */
declare(strict_types=1);

namespace Vision\ContactUsHistory\Ui\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Vision\ContactUsHistory\Api\Data\NoteDataInterface;
use Vision\ContactUsHistory\Api\Query\GetNotesListInterface;
use Magento\Ui\DataProvider\SearchResultFactory;

class NoteDataProvider extends DataProvider
{
    /**
     * @var GetNotesListInterface
     */
    private $getNotesList;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * NoteDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param GetNotesListInterface $getNotesList
     * @param SearchResultFactory $searchResultFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        GetNotesListInterface $getNotesList,
        SearchResultFactory $searchResultFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->searchResultFactory = $searchResultFactory;
        $this->getNotesList = $getNotesList;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchResult()
    {
        $searchCriteria = $this->getSearchCriteria();
        $result = $this->getNotesList->execute($searchCriteria);

        $searchResult = $this->searchResultFactory->create(
            $result->getItems(),
            $result->getTotalCount(),
            $searchCriteria,
            NoteDataInterface::NOTE_ID
        );
        return $searchResult;
    }
}

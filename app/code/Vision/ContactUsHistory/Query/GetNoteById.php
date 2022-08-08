<?php
/**
 * @author Vitaliy Boyko <vision@i.ua>
 */
declare(strict_types=1);

namespace Vision\ContactUsHistory\Query;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Vision\ContactUsHistory\Api\Data\NoteDataInterface;
use Vision\ContactUsHistory\Api\Query\GetNoteByIdInterface;
use Vision\ContactUsHistory\Api\Query\GetNotesListInterface;

/**
 * @inheritdoc
 */
class GetNoteById implements GetNoteByIdInterface
{
    /**
     * @var GetNotesListInterface
     */
    private $getNotesList;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param GetNotesListInterface $getNotesList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        GetNotesListInterface $getNotesList,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->getNotesList = $getNotesList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $noteId): NoteDataInterface
    {
        /** @var SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(NoteDataInterface::NOTE_ID, $noteId)
            ->create();
        $items =  $this->getNotesList->execute($searchCriteria)->getItems();
        if ($items) {
            return current($items);
        } else {
            throw new NoSuchEntityException();
        }
    }
}

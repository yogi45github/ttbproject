<?php
/**
 * @author Vitaliy Boyko <vision@i.ua>
 */
declare(strict_types=1);

namespace Vision\ContactUsHistory\Service;

use Vision\ContactUsHistory\Api\Command\SaveNotesInterface;
use Vision\ContactUsHistory\Mapper\NoteDataPostMapper;

/**
 * Note processor
 */
class ProcessNoteService
{
    /**
     * @var SaveNotesInterface
     */
    private $notesSave;

    /**
     * @var NoteDataPostMapper
     */
    private $noteDataPostMapper;

    /**
     * @param SaveNotesInterface $notesSave
     * @param NoteDataPostMapper $noteDataPostMapper
     */
    public function __construct(
        SaveNotesInterface $notesSave,
        NoteDataPostMapper $noteDataPostMapper
    ) {
        $this->notesSave = $notesSave;
        $this->noteDataPostMapper = $noteDataPostMapper;
    }

    /**
     * Save Note Post
     *
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function execute(): void
    {
        $note = $this->noteDataPostMapper->map();
        $this->notesSave->execute([$note]);
    }
}

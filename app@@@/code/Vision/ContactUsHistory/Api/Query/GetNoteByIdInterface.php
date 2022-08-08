<?php
/**
 * @author Vitaliy Boyko <vision@i.ua>
 */
declare(strict_types=1);

namespace Vision\ContactUsHistory\Api\Query;

use Magento\Framework\Exception\NoSuchEntityException;
use Vision\ContactUsHistory\Api\Data\NoteDataInterface;

/**
 * Returns Note by entity id
 * @api
 */
interface GetNoteByIdInterface
{
    /**
     * @param int $noteId
     * @return NoteDataInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $noteId): NoteDataInterface;
}

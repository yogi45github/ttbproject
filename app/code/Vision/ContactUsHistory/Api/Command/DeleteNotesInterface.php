<?php
/**
 * @author Vitaliy Boyko <vision@i.ua>
 */
declare(strict_types=1);

namespace Vision\ContactUsHistory\Api\Command;

use Vision\ContactUsHistory\Api\Data\NoteDataInterface;

/**
 * @api
 */
interface DeleteNotesInterface
{
    /**
     * Delete Multiple Note
     *
     * @param NoteDataInterface[] $notes
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(array $notes): void;
}

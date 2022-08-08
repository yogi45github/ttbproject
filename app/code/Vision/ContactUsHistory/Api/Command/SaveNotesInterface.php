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
interface SaveNotesInterface
{
    /**
     * Save Multiple Note data
     *
     * @param NoteDataInterface[] $notes
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Validation\ValidationException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(array $notes): void;
}

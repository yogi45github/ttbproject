<?php
/**
 * @author Vitaliy Boyko <vision@i.ua>
 */
declare(strict_types=1);

namespace Vision\ContactUsHistory\Service\Validation;

use Magento\Framework\Exception\LocalizedException;
use Vision\ContactUsHistory\Api\Data\NoteDataInterface;

/**
 * NoteData validator
 */
class ValidateNote
{
    /**
     * @param NoteDataInterface $noteData
     * @throws LocalizedException
     */
    public function execute(NoteDataInterface $noteData)
    {
        if (trim($noteData->getContactName()) === '') {
            throw new LocalizedException(__('Contact Name is missing'));
        }
        if (trim($noteData->getMessage()) === '') {
            throw new LocalizedException(__('Message is missing'));
        }
        if (false === \strpos($noteData->getEmail(), '@')) {
            throw new LocalizedException(__('Invalid email address'));
        }
    }
}

<?php
/**
 * @author Vitaliy Boyko <vision@i.ua>
 */
declare(strict_types=1);

namespace Vision\ContactUsHistory\Command;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\InputException;
use Vision\ContactUsHistory\Api\Command\DeleteNotesInterface;
use Psr\Log\LoggerInterface;
use Vision\ContactUsHistory\Command\Resource\DeleteNotes;

/**
 * @inheritdoc
 */
class DeleteNotesCommand implements DeleteNotesInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DeleteNotes
     */
    private $deleteNotes;

    /**
     * @param LoggerInterface $logger
     * @param DeleteNotes $deleteNotes
     */
    public function __construct(
        LoggerInterface $logger,
        DeleteNotes $deleteNotes
    ) {
        $this->logger = $logger;
        $this->deleteNotes = $deleteNotes;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $notes): void
    {
        if (empty($notes)) {
            throw new InputException(__('Input data is empty'));
        }
        try {
            $this->deleteNotes->execute($notes);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotDeleteException(__('Could not delete notes'), $e);
        }
    }
}

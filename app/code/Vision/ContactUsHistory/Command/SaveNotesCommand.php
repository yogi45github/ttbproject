<?php
/**
 * @author Vitaliy Boyko <vision@i.ua>
 */
declare(strict_types=1);

namespace Vision\ContactUsHistory\Command;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Psr\Log\LoggerInterface;
use Vision\ContactUsHistory\Api\Command\SaveNotesInterface;
use Vision\ContactUsHistory\Command\Resource\SaveNotes;

/**
 * @inheritdoc
 */
class SaveNotesCommand implements SaveNotesInterface
{
    /**
     * @var SaveNotes
     */
    private $saveMultiple;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param SaveNotes $saveMultiple
     * @param LoggerInterface $logger
     */
    public function __construct(
        SaveNotes $saveMultiple,
        LoggerInterface $logger
    ) {
        $this->saveMultiple = $saveMultiple;
        $this->logger = $logger;
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
            $this->saveMultiple->execute($notes);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save notes'), $e);
        }
    }
}

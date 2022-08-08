<?php
/**
 * @author Vitaliy Boyko <vision@i.ua>
 */
declare(strict_types=1);

namespace Vision\ContactUsHistory\Command\Resource;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Vision\ContactUsHistory\Api\Data\NoteDataInterface;
use Vision\ContactUsHistory\Model\ResourceModel\NoteResource;
use Vision\ContactUsHistory\Service\Validation\ValidateNote;

/**
 * Implementation of Notes save multiple operation for specific db layer
 */
class SaveNotes
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ValidateNote
     */
    private $validateNote;

    /**
     * @param ResourceConnection $resourceConnection
     * @param ValidateNote $validateNote
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ValidateNote $validateNote
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->validateNote = $validateNote;
    }

    /**
     * Multiple save notes
     *
     * @param NoteDataInterface[] $notes
     * @return void
     * @throws LocalizedException
     */
    public function execute(array $notes): void
    {
        if (!count($notes)) {
            return;
        }
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(NoteResource::TABLE_NAME_NOTES);
        $columnsSql = $this->buildColumnsSqlPart([
            NoteDataInterface::NOTE_ID,
            NoteDataInterface::PHONE,
            NoteDataInterface::CONTACT_NAME,
            NoteDataInterface::MESSAGE,
            NoteDataInterface::EMAIL,
            NoteDataInterface::CUSTOMER_ID
        ]);
        $valuesSql = $this->buildValuesSqlPart($notes);
        $onDuplicateSql = $this->buildOnDuplicateSqlPart([
            NoteDataInterface::PHONE,
            NoteDataInterface::CONTACT_NAME,
            NoteDataInterface::MESSAGE,
            NoteDataInterface::EMAIL,
            NoteDataInterface::CUSTOMER_ID
        ]);
        $bind = $this->getSqlBindData($notes);
        $insertSql = sprintf(
            'INSERT INTO %s (%s) VALUES %s %s',
            $tableName,
            $columnsSql,
            $valuesSql,
            $onDuplicateSql
        );
        $connection->query($insertSql, $bind);
    }

    /**
     * @param array $columns
     * @return string
     */
    private function buildColumnsSqlPart(array $columns): string
    {
        $connection = $this->resourceConnection->getConnection();
        $processedColumns = array_map([$connection, 'quoteIdentifier'], $columns);
        $sql = implode(', ', $processedColumns);
        return $sql;
    }

    /**
     * @param NoteDataInterface[] $notes
     * @return string
     */
    private function buildValuesSqlPart(array $notes): string
    {
        $sql = rtrim(str_repeat('(?, ?, ?, ?, ?, ?), ', count($notes)), ', ');
        return $sql;
    }

    /**
     * @param NoteDataInterface[] $notes
     * @return array
     * @throws LocalizedException
     */
    private function getSqlBindData(array $notes): array
    {
        $bind = [];
        foreach ($notes as $note) {
            $this->validateNote->execute($note);
            $bind = array_merge($bind, [
                $note->getNoteId(),
                $note->getPhone(),
                $note->getContactName(),
                $note->getMessage(),
                $note->getEmail(),
                $note->getCustomerId()
            ]);
        }
        return $bind;
    }

    /**
     * @param array $fields
     * @return string
     */
    private function buildOnDuplicateSqlPart(array $fields): string
    {
        $connection = $this->resourceConnection->getConnection();
        $processedFields = [];
        foreach ($fields as $field) {
            $processedFields[] = sprintf('%1$s = VALUES(%1$s)', $connection->quoteIdentifier($field));
        }
        $sql = 'ON DUPLICATE KEY UPDATE ' . implode(', ', $processedFields);
        return $sql;
    }
}
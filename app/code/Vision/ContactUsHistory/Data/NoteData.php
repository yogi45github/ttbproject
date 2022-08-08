<?php
/**
 * @author Vitaliy Boyko <vision@i.ua>
 */
declare(strict_types=1);

namespace Vision\ContactUsHistory\Data;

use Magento\Framework\DataObject;
use Vision\ContactUsHistory\Api\Data\NoteDataInterface;

/**
 * @inheritdoc
 */
class NoteData extends DataObject implements NoteDataInterface
{
    /**
     * @inheritdoc
     */
    public function getNoteId(): ?int
    {
        return $this->getData(self::NOTE_ID) === null ?
            null:
            (int)$this->getData(self::NOTE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setNoteId(int $noteId): void
    {
        $this->setData(self::NOTE_ID, $noteId);
    }

    /**
     * @inheritdoc
     */
    public function getEmail(): ?string
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setEmail(string $email): void
    {
        $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritdoc
     */
    public function getContactName(): ?string
    {
        return $this->getData(self::CONTACT_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setContactName(string $contactName): void
    {
        $this->setData(self::CONTACT_NAME, $contactName);
    }

    /**
     * @inheritdoc
     */
    public function getMessage(): ?string
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @inheritdoc
     */
    public function setMessage(string $message): void
    {
        $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritdoc
     */
    public function getPhone(): ?string
    {
        return $this->getData(self::PHONE);
    }

    /**
     * @inheritdoc
     */
    public function setPhone(?string $phone): void
    {
        $this->setData(self::PHONE, $phone);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedDate(): ?string
    {
        return $this->getData(self::CREATED_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedDate(string $createdDate): void
    {
        $this->setData(self::CREATED_DATE, $createdDate);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId(): ?int
    {
        return $this->getData(self::CUSTOMER_ID) === null ?
            null:
            (int)$this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId(?int $customerId): void
    {
        $this->setData(self::CUSTOMER_ID, $customerId);
    }
}

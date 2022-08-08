<?php
/**
 * @author Vitaliy Boyko <vision@i.ua>
 */
declare(strict_types=1);

namespace Vision\ContactUsHistory\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Vision\ContactUsHistory\Api\Data\NoteDataInterface;

class NoteResource extends AbstractDb
{
    /**
     * Table name
     */
    const TABLE_NAME_NOTES = 'vb_contact_us_history';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME_NOTES, NoteDataInterface::NOTE_ID);
    }
}

<?php
/**
 * @author Vitaliy Boyko <vision@i.ua>
 */
declare(strict_types=1);

namespace Vision\ContactUsHistory\Model\ResourceModel\Note;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Vision\ContactUsHistory\Api\Data\NoteDataInterface;
use Vision\ContactUsHistory\Model\NoteModel;
use Vision\ContactUsHistory\Model\ResourceModel\NoteResource;

/**
 * @inheritdoc
 */
class NoteCollection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(NoteModel::class, NoteResource::class);
        $this->_setIdFieldName(NoteDataInterface::NOTE_ID);
    }
}

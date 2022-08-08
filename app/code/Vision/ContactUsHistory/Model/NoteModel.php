<?php
/**
 * @author Vitaliy Boyko <vision@i.ua>
 */
declare(strict_types=1);

namespace Vision\ContactUsHistory\Model;

use Magento\Framework\Model\AbstractModel;
use Vision\ContactUsHistory\Model\ResourceModel\NoteResource;

/**
 * Note Model
 */
class NoteModel extends AbstractModel
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(NoteResource::class);
    }
}

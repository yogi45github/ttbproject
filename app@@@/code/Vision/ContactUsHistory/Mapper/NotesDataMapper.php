<?php
/**
 * @author Vitaliy Boyko <vision@i.ua>
 */
declare(strict_types=1);

namespace Vision\ContactUsHistory\Mapper;

use Magento\Framework\Api\DataObjectHelper;
use Vision\ContactUsHistory\Api\Data\NoteDataInterface;
use Vision\ContactUsHistory\Api\Data\NoteDataInterfaceFactory;
use Vision\ContactUsHistory\Model\NoteModel;
use Vision\ContactUsHistory\Model\ResourceModel\Note\NoteCollection;

/**
 * Class NoteDataMapper
 * Transfers data from NoteModel's to NoteData's
 */
class NotesDataMapper
{
    /**
     * @var NoteDataInterfaceFactory
     */
    private $noteDataInterfaceFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param NoteDataInterfaceFactory $noteDataInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        NoteDataInterfaceFactory $noteDataInterfaceFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->noteDataInterfaceFactory = $noteDataInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Map data models
     *
     * @param NoteCollection $noteCollection
     * @return NoteDataInterface[]
     */
    public function map(NoteCollection $noteCollection): array
    {
        $noteModels = $noteCollection->getItems();
        $noteDataObjects = [];
        foreach ($noteModels as $noteModel) {
            /** @var NoteModel $noteDataObject */
            $noteDataObject = $this->noteDataInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $noteDataObject,
                $noteModel->getData(),
                NoteDataInterface::class
            );
            $noteDataObjects[] = $noteDataObject;
        }

        return $noteDataObjects;
    }
}

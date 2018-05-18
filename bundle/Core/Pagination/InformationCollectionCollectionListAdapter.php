<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Pagination;

use Netgen\Bundle\InformationCollectionBundle\API\InformationCollectionService;
use Pagerfanta\Adapter\AdapterInterface;

class InformationCollectionCollectionListAdapter implements AdapterInterface
{
    /**
     * @var InformationCollectionService
     */
    protected $informationCollectionService;

    /**
     * @var int
     */
    protected $contentId;

    public function __construct(InformationCollectionService $informationCollectionService, $contentId)
    {
        $this->informationCollectionService = $informationCollectionService;
        $this->contentId = $contentId;
    }

    public function getNbResults()
    {
        if (!isset($this->nbResults)) {
            $this->nbResults = $this->informationCollectionService->collectionListCount($this->contentId);
        }

        return $this->nbResults;
    }

    public function getSlice($offset, $length)
    {
        $objects = $this->informationCollectionService
            ->collectionList($this->contentId, $length, $offset);

        if (!isset($this->nbResults)) {
            $this->nbResults = $this->informationCollectionService->collectionListCount($this->contentId);
        }

        return $objects;
    }
}
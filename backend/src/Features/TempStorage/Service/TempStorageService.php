<?php

namespace App\Features\TempStorage\Service;

use App\Document\Storage\Temp\TempStorage;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;

readonly class TempStorageService
{
    public function __construct(
        private DocumentManager $documentManager,
    ) {
    }

    /**
     * @throws MongoDBException
     */
    public function save(TempStorage $tempStorage): string
    {
        $this->documentManager->persist($tempStorage);
        $this->documentManager->flush();

        return $tempStorage->getId();
    }

    /**
     * @throws MongoDBException
     */
    public function delete(TempStorage $tempStorage): void
    {
        $this->documentManager->remove($tempStorage);
        $this->documentManager->flush();
    }
}
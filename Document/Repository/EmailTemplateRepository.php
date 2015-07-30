<?php

namespace Redking\Bundle\MailBundle\Document\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class EmailTemplateRepository extends DocumentRepository
{
    /**
     * Retourne les name existants
     * @return array
     */
    public function getExistingNames()
    {
        return array_keys($this->createQueryBuilder()
            ->select('name')
            ->hydrate(false)
            ->getQuery()
            ->execute()
            ->toArray());
    }

}

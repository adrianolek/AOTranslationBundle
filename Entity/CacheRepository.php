<?php

namespace AO\TranslationBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CacheRepository extends EntityRepository
{
    public function resetActionCache($key)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $q = $qb->delete('AOTranslationBundle:Cache', 'c')
            ->where('c.bundle=:bundle AND c.controller=:controller AND c.action=:action')->getQuery();
        $q->execute($key);
    }

    public function resetCache()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $q = $qb->delete('AOTranslationBundle:Cache', 'c')->getQuery();
        $q->execute();
    }
}

<?php

namespace OC\PlatformBundle\Repository;

/**
 * AdvertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdvertRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAdvertWithCategories1(array $categoryNames)
    {
        // Méthode 2 : en passant par le raccourci (je recommande)
        $queryBuilder = $this->createQueryBuilder('a');

        /*$queryBuilder
            ->where('a.id = :id')
            ->setParameter('id', 1);*/

        $queryBuilder->leftJoin('a.categories', 'cat');
        $test = '';
        foreach ($categoryNames as $key => $name) {
            if (count($categoryNames) == 1 || $key < count($categoryNames)-1) {
                $queryBuilder->where("cat.name = :name$key")->setParameter("name$key", $name);
                $test.= 'inner';
                continue;
            }
            $test.= 'outer';
            $queryBuilder->orWhere("cat.name = :name$key")->setParameter("name$key", $name);
        }
        echo $test;



        // On récupère la Query à partir du QueryBuilder
        $query = $queryBuilder->getQuery();
        // On récupère les résultats à partir de la Query
        $results = $query->getResult();

        return $results;
    }

    public function getAdvertWithCategories2(array $categoryNames)
    {
        // Méthode 1 : en passant par l'EntityManager
        $queryBuilder = $this->_em->createQueryBuilder()
          ->select('a')
          ->from($this->_entityName, 'a');
    }

    public function getAdvertWithCategories(array $categoryNames)
    {
        $qb = $this->createQueryBuilder('a');

        // On fait une jointure avec l'entité Category avec pour alias « c »
        $qb
          ->innerJoin('a.categories', 'c')
          ->addSelect('c')
        ;

        // Puis on filtre sur le nom des catégories à l'aide d'un IN
        $qb->where($qb->expr()->in('c.name', $categoryNames));
        // La syntaxe du IN et d'autres expressions se trouve dans la documentation Doctrine

        // Enfin, on retourne le résultat
        return $qb
          ->getQuery()
          ->getResult()
        ;
    }
}

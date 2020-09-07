<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Client;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository
{
    private $httpClient;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
        $this->httpClient = new Client();
    }

    public function getCurrencyExchange($originCurrency, $destinationCarrency) {
        $res = $this->httpClient->request(
            "GET",
            "https://api.exchangeratesapi.io/latest?base=$originCurrency&symbols=$destinationCarrency"
        );
        if ($res->getStatusCode() !== 200) {
            throw new HttpException(400, "Ocurrio un error al obtener el cambio");
        }
        return json_decode((string)$res->getBody());
    }

    /**
     * @param array $arrFilters
     * @return Player[]|null
     */
    public function findBySomeField(array $arrFilters): ?array
    {
        $queryBuilder = $this->createQueryBuilder("player");
        $queryBuilder->join('player.team', 'team');
        $firstWhere = false;
        foreach ($arrFilters as $filterName => $filterValue) {
            if (strpos($filterName, '.') === false) {
                if ($firstWhere === false) {
                    $queryBuilder->where("player.$filterName = '$filterValue'");
                    $firstWhere = true;
                } else {
                    $queryBuilder->andWhere("player.$filterName = '$filterValue'");
                }
            } else {
                if ($firstWhere === false) {
                    $queryBuilder->where("$filterName = '$filterValue'");
                    $firstWhere = true;
                } else {
                    $queryBuilder->andWhere("$filterName = '$filterValue'");
                }
            }
        }
        return $queryBuilder->getQuery()->getResult();
    }
}

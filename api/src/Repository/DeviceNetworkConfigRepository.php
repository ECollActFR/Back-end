<?php

namespace App\Repository;

use App\Entity\DeviceNetworkConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DeviceNetworkConfig>
 */
class DeviceNetworkConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviceNetworkConfig::class);
    }
}

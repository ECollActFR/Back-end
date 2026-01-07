<?php

namespace App\Repository;

use App\Entity\DeviceSystemConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DeviceSystemConfig>
 */
class DeviceSystemConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviceSystemConfig::class);
    }
}

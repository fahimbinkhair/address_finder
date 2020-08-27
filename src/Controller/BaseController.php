<?php
declare(strict_types=1);
/**
 * Description:
 * holds basic functionality for all controllers
 *
 * @package App\Controller
 */

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class BaseController
 *
 * @package App\Controller
 */
class BaseController extends AbstractController
{
    /** @var EntityManagerInterface $em */
    protected $em;

    /**
     * @required
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->em = $entityManager;
    }
}

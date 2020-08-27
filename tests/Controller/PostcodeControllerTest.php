<?php
declare(strict_types=1);
/**
 * Description:
 * test App\Controller\PostcodeController
 *
 * @package App\Tests\Controller
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class PostcodeControllerTest
 *
 * @package App\Tests\Controller
 */
class PostcodeControllerTest extends WebTestCase
{
    /**
     * @group Api
     */
    public function testCanReturnNearestLocationsByLatitudeLongitude(): void
    {
        /** @var string $api */
        $api = 'http://127.0.0.1:8000/get-postcodes-by-latitude-longitude/57.09708500/-2.26751300/3';
        /** @var Client $client */
        $client = static::createClient();
        $client->request('GET', $api);
        /** @var string postcodes */
        $postcodes = $client->getResponse()->getContent();
        $this->assertEquals(584, count(json_decode($postcodes, true)), 'Failed to get expected number of postcodes');
    }
}

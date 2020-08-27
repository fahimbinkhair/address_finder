<?php
declare(strict_types=1);
/**
 * Description:
 *
 * @package App\Services
 */

namespace App\Controller;

use App\Entity\PostcodeInfo;
use App\Repository\PostcodeInfoRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostcodeController
 *
 * @package App\Controller
 */
class PostcodeController extends BaseController
{
    /**
     * @Route(
     *     "/get-postcodes/{postcodePart}",
     *     requirements={"postcode"="^[A-Za-z0-9 ]+$"},
     *     name="getPostcodes",
     *     methods={"GET"}
     * )
     * @param string $postcodePart
     * @return JsonResponse
     */
    public function getPostcodes(string $postcodePart): JsonResponse
    {
        /** @var PostcodeInfoRepository $postcodeInfoRepository */
        $postcodeInfoRepository = $this->em->getRepository(PostcodeInfo::class);
        $matchingPostcodes = $postcodeInfoRepository->getMatchingPostcodes($postcodePart);

        return $this->json($matchingPostcodes);
    }

    /**
     * @Route(
     *     "/get-postcodes-by-latitude-longitude/{latitude}/{longitude}/{withinNMiles}",
     *     requirements={"latitude"="^[0-9.]+$", "longitude"="^[\-0-9.]+$"},
     *     name="getPostcodesByLatitudeLongitude",
     *     methods={"GET"}
     * )
     * @param $latitude
     * @param $longitude
     * @param $withinNMiles
     * @return JsonResponse
     */
    public function getPostcodesByLatitudeLongitude($latitude, $longitude, $withinNMiles): JsonResponse
    {
        /** @var PostcodeInfoRepository $postcodeInfoRepository */
        $postcodeInfoRepository = $this->em->getRepository(PostcodeInfo::class);
        $matchingPostcodes = $postcodeInfoRepository->getPostcodesNearALocation($latitude, $longitude, $withinNMiles);

        return $this->json($matchingPostcodes);
    }
}

<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Tests\Controller;
use Lakion\ApiTestCase\JsonApiTestCase;
use \Sylius\Component\Promotion\Model\PromotionInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class PromotionApiTest extends JsonApiTestCase
{
    /**
     * @var array
     */
    private static $authorizedHeaderWithAccept = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'ACCEPT' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_does_not_allow_to_show_promotions_list_when_access_is_denied()
    {
        $this->loadFixturesFromFile('resources/promotions.yml');

        $this->client->request('GET', '/api/v1/promotions/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_indexing_promotions()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/promotions.yml');


        $this->client->request('GET', '/api/v1/promotions/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_show_promotion_when_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/promotions/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_showing_promotion()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $promotions = $this->loadFixturesFromFile('resources/promotions.yml');
        $promotion = $promotions['promotion2'];

        $this->client->request('GET', $this->getPromotionUrl($promotion), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/show_response', Response::HTTP_OK);
    }

    /**
     * @param PromotionInterface $promotion
     *
     * @return string
     */
    private function getPromotionUrl(PromotionInterface $promotion)
    {
        return '/api/v1/promotions/' . $promotion->getCode();
    }
}

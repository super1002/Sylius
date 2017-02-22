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
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProductAttributeApiTest extends JsonApiTestCase
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
    public function it_does_not_allow_to_show_product_attributes_list_when_access_is_denied()
    {
        $this->loadFixturesFromFile('resources/product_attributes.yml');

        $this->client->request('GET', '/api/v1/product-attributes/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_indexing_product_attributes()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/product_attributes.yml');


        $this->client->request('GET', '/api/v1/product-attributes/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product_attribute/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_show_product_attribute_when_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/product-attributes/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_showing_product_attribute()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productAttributes = $this->loadFixturesFromFile('resources/product_attributes.yml');
        $productAttribute = $productAttributes['productAttribute1'];

        $this->client->request('GET', $this->getProductAttributeUrl($productAttribute), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product_attribute/show_response', Response::HTTP_OK);
    }

    /**
     * @param ProductAttributeInterface $productAttribute
     *
     * @return string
     */
    private function getProductAttributeUrl(ProductAttributeInterface $productAttribute)
    {
        return '/api/v1/product-attributes/' . $productAttribute->getCode();
    }
}

<?php

namespace spec\Sylius\Bundle\ApiBundle\ApiPlatform;

use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\ApiPlatform\ConfigMergeManager;

class ResourceMetadataPropertyValueResolverSpec extends ObjectBehavior
{
    function let(ConfigMergeManager $configMergeManager)
    {
        $this->beConstructedWith($configMergeManager);
    }

    function it_returns_merged_configs(ConfigMergeManager $configMergeManager): void {
        $resourceMetadata = new ResourceMetadata(
            null,
            null,
            null,
            [
                'admin_get' => [
                    'method' => 'GET',
                    'path' => 'admin/path/{tokenValue}'
                ],
                'admin_post' => [
                    'method' => 'POST',
                    'path' => 'admin/path'
                ]
            ]
        );

        $configMergeManager->mergeConfigs(
            [
                'admin_get' => [
                    'method' => 'GET',
                    'path' => 'admin/path/{tokenValue}'
                ],
                'admin_post' => [
                    'method' => 'POST',
                    'path' => 'admin/path'
                ]
            ],
            [
                'admin_get (unset)' => null,
            ]
        )->willReturn(
            [
                'admin_post' => [
                    'method' => 'POST',
                    'path' => 'admin/path'
                ]
            ]
        );

        $this->resolve(
            'itemOperations',
            $resourceMetadata,
            [
                'itemOperations' => [
                    'admin_get (unset)' => null,
                ]
            ]
        )->shouldReturn(
            [
                'admin_post' => [
                    'method' => 'POST',
                    'path' => 'admin/path'
                ]
            ]
        );
    }

    public function it_returns_parent_config_if_child_config_is_null(ConfigMergeManager $configMergeManager): void
    {
        $resourceMetadata = new ResourceMetadata(
            null,
            null,
            null,
            [
                'admin_get' => [
                    'method' => 'GET',
                    'path' => 'admin/path/{tokenValue}'
                ],
                'admin_post' => [
                    'method' => 'POST',
                    'path' => 'admin/path'
                ]
            ]
        );

        $configMergeManager->mergeConfigs(
            Argument::any()
        )->shouldNotBeCalled();

        $this->resolve(
            'itemOperations',
            $resourceMetadata,
            [
                'itemOperations' => null
            ]
        )->shouldReturn(
            [
                'admin_get' => [
                    'method' => 'GET',
                    'path' => 'admin/path/{tokenValue}'
                ],
                'admin_post' => [
                    'method' => 'POST',
                    'path' => 'admin/path'
                ]
            ]
        );
    }

    public function it_returns_child_config_if_parent_config_is_null(ConfigMergeManager $configMergeManager): void
    {
        $resourceMetadata = new ResourceMetadata(
            null,
            null,
            null,
            null
        );

        $configMergeManager->mergeConfigs(
            Argument::any()
        )->shouldNotBeCalled();

        $this->resolve(
            'itemOperations',
            $resourceMetadata,
            [
                'itemOperations' => [
                    'admin_patch' => [
                        'method' => 'PATCH',
                        'path' => 'admin/path'
                    ]
                ]
            ]
        )->shouldReturn(
            [
                'admin_patch' => [
                    'method' => 'PATCH',
                    'path' => 'admin/path'
                ]
            ]
        );
    }

    public function it_overwrites_parent_config(ConfigMergeManager $configMergeManager): void
    {
        $resourceMetadata = new ResourceMetadata(
            'parent_short_name',
            null,
            null,
        );

        $configMergeManager->mergeConfigs(
            Argument::any()
        )->shouldNotBeCalled();

        $this->resolve(
            'shortName',
            $resourceMetadata,
            [
                'shortName' => 'child_short_name'
            ]
        )->shouldReturn('child_short_name');
    }

    public function it_throws_error_if_parent_data_is_array_and_child_type_is_different(ConfigMergeManager $configMergeManager): void
    {
        $resourceMetadata = new ResourceMetadata(
            'parent_short_name',
            null,
            null,
            null,
            [
                'admin_get' => [
                    'method' => 'GET',
                    'path' => 'admin/path/{tokenValue}'
                ],
                'admin_post' => [
                    'method' => 'POST',
                    'path' => 'admin/path'
                ]
            ]
        );

        $configMergeManager->mergeConfigs(
            Argument::any()
        )->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('resolve', [
            'collectionOperations',
            $resourceMetadata,
            [
                'collectionOperations' => 'invalid_string'
            ]
        ]);
    }
}

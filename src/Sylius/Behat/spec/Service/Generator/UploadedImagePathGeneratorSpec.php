<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Behat\Service\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Generator\ImagePathGeneratorInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadedImagePathGeneratorSpec extends ObjectBehavior
{
    function it_implements_image_path_generator_interface(): void
    {
        $this->shouldImplement(ImagePathGeneratorInterface::class);
    }

    function it_generates_random_hashed_path_keeping_the_image_name(ImageInterface $image): void
    {
        $file = new UploadedFile(__DIR__ . '/ford.jpg', 'ford.jpg', null, null, true);

        $image->getFile()->willReturn($file);

        $this->generate($image)->shouldMatch('/[a-z0-9]{2}\/[a-z0-9]{2}\/[a-zA-Z0-9]+[_-]*\/ford[.]jpg/i');
    }
}

<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\InformationMap;

use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationProvider;
use PHPUnit\Framework\TestCase;

final class ActionBlockInformationProviderTest extends TestCase
{
    public function testProvideReturnsInformationFromProvidedData(): void
    {
        $provider = new ActionBlockInformationProvider([
            [
                'blockName' => 'contact-form',
                'title' => 'Contact form',
                'identifier' => 'App\\Action\\ContactFormAction',
                'needsGeneration' => true,
            ],
        ]);
        $collection = $provider->provide();
        $information = $collection->first();

        self::assertNotNull($information);
        self::assertSame('contact-form', $information->blockName);
        self::assertSame('Contact form', $information->title);
        self::assertSame('App\\Action\\ContactFormAction', $information->identifier);
        self::assertTrue($information->needsGeneration);
    }
}

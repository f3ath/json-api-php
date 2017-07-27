<?php
/**
 *  This file is part of JSON:API implementation for PHP.
 *
 *  (c) Alexey Karapetov <karapetov@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
declare(strict_types=1);

namespace JsonApiPhp\JsonApi\Test\Document;

use JsonApiPhp\JsonApi\Document;
use JsonApiPhp\JsonApi\Document\ArrayMeta;
use JsonApiPhp\JsonApi\Document\Error;
use JsonApiPhp\JsonApi\Document\Resource\NullResource;
use JsonApiPhp\JsonApi\Document\Resource\ResourceIdentifier;
use JsonApiPhp\JsonApi\Test\BaseTestCase;

class DocumentTest extends BaseTestCase
{
    public function testCanCreateFromMeta()
    {
        $this->assertEqualsAsJson(
            ['meta' => ['foo' => 'bar']],
            Document::fromMeta(new ArrayMeta(['foo' => 'bar']))
        );
    }

    public function testCanCreateFromErrors()
    {
        $this->assertEqualsAsJson(
            ['errors' => []],
            Document::fromErrors()
        );

        $this->assertEqualsAsJson(
            [
                'errors' => [
                    ['id' => 'first'],
                ],
            ],
            $this->createErrorDoc()
        );
    }

    public function testCanCreateNullDocument()
    {
        $this->assertEqualsAsJson(
            ['data' => null],
            $this->createNullDoc()
        );
    }

    public function testCanCreateFromResourceId()
    {
        $this->assertEqualsAsJson(
            [
                'data' => [
                    'type' => 'books',
                    'id' => 'abc123',
                ],
            ],
            Document::fromResource(new ResourceIdentifier('books', 'abc123'))
        );
    }

    public function testCanCreateFromMultipleItems()
    {
        $this->assertEqualsAsJson(
            [
                'data' => [],
            ],
            Document::fromResources()
        );

        $this->assertEqualsAsJson(
            [
                'data' => [
                    [
                        'type' => 'books',
                        'id' => '12',
                    ],
                    [
                        'type' => 'carrots',
                        'id' => '42',
                    ],
                ],
            ],
            Document::fromResources(
                new ResourceIdentifier('books', '12'),
                new ResourceIdentifier('carrots', '42')
            )
        );
    }

    public function testDocumentMayContainVersion()
    {
        $doc = $this->createNullDoc()
            ->withApiVersion('1.2.3')
            ->withApiMeta(['a' => 'b']);
        $this->assertEqualsAsJson(
            [
                'data' => null,
                'jsonapi' => [
                    'version' => '1.2.3',
                    'meta' => ['a' => 'b'],
                ],
            ],
            $doc
        );
    }

    public function testDocumentMayContainLinks()
    {
        $doc = $this->createNullDoc()
            ->withLink('self', 'http://example.com/self')
            ->withLinkObject('related', 'http://example.com/rel', new ArrayMeta(['a' => 'b']));
        $this->assertEqualsAsJson(
            [
                'data' => null,
                'links' => [
                    'self' => 'http://example.com/self',
                    'related' => [
                        'href' => 'http://example.com/rel',
                        'meta' => ['a' => 'b'],
                    ],
                ],
            ],
            $doc
        );
    }

    private function createErrorDoc(): Document
    {
        return Document::fromErrors(
            (new Error())->withId('first')
        );
    }

    private function createNullDoc(): Document
    {
        return Document::fromResource(new NullResource);
    }
}

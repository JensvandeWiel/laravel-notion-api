<?php

use Jensvandewiel\LaravelNotionApi\Entities\PropertyItems\RichText;
use Jensvandewiel\LaravelNotionApi\Entities\PropertyItems\RichTextItem;
use Jensvandewiel\LaravelNotionApi\Entities\PropertyItems\Annotation;
use Jensvandewiel\LaravelNotionApi\Entities\PropertyItems\RichTextMention;

/** @test */
it('parses plain text items', function () {
    $data = [
        [
            'type' => 'text',
            'text' => [
                'content' => 'Hello ',
                'link' => null,
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'Hello ',
            'href' => null,
        ],
        [
            'type' => 'text',
            'text' => [
                'content' => 'world',
                'link' => null,
            ],
            'annotations' => [
                'bold' => true,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'world',
            'href' => null,
        ],
    ];

    $richText = new RichText($data);

    expect($richText->getPlainText())->toBe('Hello world');
    expect($richText->getItems())->toHaveCount(2);
    expect($richText->getItem(0)->getType())->toBe('text');
    expect($richText->getItem(1)->getAnnotations()->isBold())->toBeTrue();
});

/** @test */
it('parses text with link', function () {
    $data = [
        [
            'type' => 'text',
            'text' => [
                'content' => 'click here',
                'link' => [
                    'url' => 'https://example.com',
                ],
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'click here',
            'href' => 'https://example.com',
        ],
    ];

    $richText = new RichText($data);
    $item = $richText->getItem(0);

    expect($item->hasTextLink())->toBeTrue();
    expect($item->getTextLinkUrl())->toBe('https://example.com');
    expect($item->hasLink())->toBeTrue();
});

/** @test */
it('parses text with all annotations', function () {
    $data = [
        [
            'type' => 'text',
            'text' => [
                'content' => 'Styled text',
                'link' => null,
            ],
            'annotations' => [
                'bold' => true,
                'italic' => true,
                'strikethrough' => true,
                'underline' => true,
                'code' => true,
                'color' => 'red',
            ],
            'plain_text' => 'Styled text',
            'href' => null,
        ],
    ];

    $richText = new RichText($data);
    $item = $richText->getItem(0);
    $annotations = $item->getAnnotations();

    expect($annotations->isBold())->toBeTrue();
    expect($annotations->isItalic())->toBeTrue();
    expect($annotations->isStrikethrough())->toBeTrue();
    expect($annotations->isUnderline())->toBeTrue();
    expect($annotations->isCode())->toBeTrue();
    expect($annotations->getColor())->toBe('red');
    expect($annotations->hasAnyAnnotation())->toBeTrue();
});

/** @test */
it('detects default annotations', function () {
    $data = [
        [
            'type' => 'text',
            'text' => [
                'content' => 'Plain text',
                'link' => null,
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'Plain text',
            'href' => null,
        ],
    ];

    $richText = new RichText($data);
    $annotations = $richText->getItem(0)->getAnnotations();

    expect($annotations->hasAnyAnnotation())->toBeFalse();
});

/** @test */
it('parses user mention', function () {
    $data = [
        [
            'type' => 'mention',
            'mention' => [
                'type' => 'user',
                'user' => [
                    'object' => 'user',
                    'id' => 'b2e19928-b427-4aad-9a9d-fde65479b1d9',
                ],
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => '@Anonymous',
            'href' => null,
        ],
    ];

    $richText = new RichText($data);
    $item = $richText->getItem(0);

    expect($item->isMention())->toBeTrue();
    expect($item->getMention())->toBeInstanceOf(RichTextMention::class);
    expect($item->getMention()->isUser())->toBeTrue();
    expect($item->getMention()->getUserId())->toBe('b2e19928-b427-4aad-9a9d-fde65479b1d9');
});

/** @test */
it('parses page mention', function () {
    $data = [
        [
            'type' => 'mention',
            'mention' => [
                'type' => 'page',
                'page' => [
                    'id' => '3c612f56-fdd0-4a30-a4d6-bda7d7426309',
                ],
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'This is a test page',
            'href' => 'https://www.notion.so/3c612f56fdd04a30a4d6bda7d7426309',
        ],
    ];

    $richText = new RichText($data);
    $item = $richText->getItem(0);

    expect($item->getMention()->isPage())->toBeTrue();
    expect($item->getMention()->getPageId())->toBe('3c612f56-fdd0-4a30-a4d6-bda7d7426309');
    expect($item->hasLink())->toBeTrue();
});

/** @test */
it('parses database mention', function () {
    $data = [
        [
            'type' => 'mention',
            'mention' => [
                'type' => 'database',
                'database' => [
                    'id' => 'a1d8501e-1ac1-43e9-a6bd-ea9fe6c8822b',
                ],
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'Database with test things',
            'href' => 'https://www.notion.so/a1d8501e1ac143e9a6bdea9fe6c8822b',
        ],
    ];

    $richText = new RichText($data);
    $item = $richText->getItem(0);

    expect($item->getMention()->isDatabase())->toBeTrue();
    expect($item->getMention()->getDatabaseId())->toBe('a1d8501e-1ac1-43e9-a6bd-ea9fe6c8822b');
});

/** @test */
it('parses date mention', function () {
    $data = [
        [
            'type' => 'mention',
            'mention' => [
                'type' => 'date',
                'date' => [
                    'start' => '2022-12-16',
                    'end' => null,
                ],
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => '2022-12-16',
            'href' => null,
        ],
    ];

    $richText = new RichText($data);
    $item = $richText->getItem(0);

    expect($item->getMention()->isDate())->toBeTrue();
    expect($item->getMention()->getDateStart())->toBe('2022-12-16');
    expect($item->getMention()->getDateEnd())->toBeNull();
});

/** @test */
it('parses link preview mention', function () {
    $data = [
        [
            'type' => 'mention',
            'mention' => [
                'type' => 'link_preview',
                'link_preview' => [
                    'url' => 'https://example.com/page',
                ],
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'https://example.com/page',
            'href' => 'https://example.com/page',
        ],
    ];

    $richText = new RichText($data);
    $item = $richText->getItem(0);

    expect($item->getMention()->isLinkPreview())->toBeTrue();
    expect($item->getMention()->getLinkPreviewUrl())->toBe('https://example.com/page');
});

/** @test */
it('parses template mention date', function () {
    $data = [
        [
            'type' => 'mention',
            'mention' => [
                'type' => 'template_mention',
                'template_mention' => [
                    'type' => 'template_mention_date',
                    'template_mention_date' => 'today',
                ],
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => '@Today',
            'href' => null,
        ],
    ];

    $richText = new RichText($data);
    $item = $richText->getItem(0);

    expect($item->getMention()->isTemplateMention())->toBeTrue();
    expect($item->getMention()->getTemplateMentionType())->toBe('template_mention_date');
    expect($item->getMention()->getTemplateMentionDate())->toBe('today');
});

/** @test */
it('parses template mention user', function () {
    $data = [
        [
            'type' => 'mention',
            'mention' => [
                'type' => 'template_mention',
                'template_mention' => [
                    'type' => 'template_mention_user',
                    'template_mention_user' => 'me',
                ],
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => '@Me',
            'href' => null,
        ],
    ];

    $richText = new RichText($data);
    $item = $richText->getItem(0);

    expect($item->getMention()->isTemplateMention())->toBeTrue();
    expect($item->getMention()->getTemplateMentionType())->toBe('template_mention_user');
    expect($item->getMention()->getTemplateMentionUser())->toBe('me');
});

/** @test */
it('parses equation', function () {
    $data = [
        [
            'type' => 'equation',
            'equation' => [
                'expression' => 'E = mc^2',
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'E = mc^2',
            'href' => null,
        ],
    ];

    $richText = new RichText($data);
    $item = $richText->getItem(0);

    expect($item->isEquation())->toBeTrue();
    expect($item->getEquationExpression())->toBe('E = mc^2');
});

/** @test */
it('gets items by type', function () {
    $data = [
        [
            'type' => 'text',
            'text' => [
                'content' => 'Some text',
                'link' => null,
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'Some text',
            'href' => null,
        ],
        [
            'type' => 'mention',
            'mention' => [
                'type' => 'user',
                'user' => [
                    'object' => 'user',
                    'id' => 'b2e19928-b427-4aad-9a9d-fde65479b1d9',
                ],
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => '@User',
            'href' => null,
        ],
    ];

    $richText = new RichText($data);

    $textItems = $richText->getTextItems();
    $mentionItems = $richText->getMentionItems();

    expect($textItems)->toHaveCount(1);
    expect($mentionItems)->toHaveCount(1);
});

/** @test */
it('detects mentions', function () {
    $dataWithMention = [
        [
            'type' => 'mention',
            'mention' => [
                'type' => 'user',
                'user' => [
                    'object' => 'user',
                    'id' => 'b2e19928-b427-4aad-9a9d-fde65479b1d9',
                ],
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => '@User',
            'href' => null,
        ],
    ];

    $dataWithoutMention = [
        [
            'type' => 'text',
            'text' => [
                'content' => 'Plain text',
                'link' => null,
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'Plain text',
            'href' => null,
        ],
    ];

    expect((new RichText($dataWithMention))->hasMentions())->toBeTrue();
    expect((new RichText($dataWithoutMention))->hasMentions())->toBeFalse();
});

/** @test */
it('detects equations', function () {
    $dataWithEquation = [
        [
            'type' => 'equation',
            'equation' => [
                'expression' => 'E = mc^2',
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'E = mc^2',
            'href' => null,
        ],
    ];

    $dataWithoutEquation = [
        [
            'type' => 'text',
            'text' => [
                'content' => 'Plain text',
                'link' => null,
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'Plain text',
            'href' => null,
        ],
    ];

    expect((new RichText($dataWithEquation))->hasEquations())->toBeTrue();
    expect((new RichText($dataWithoutEquation))->hasEquations())->toBeFalse();
});

/** @test */
it('maintains backward compatibility', function () {
    $data = [
        [
            'type' => 'text',
            'text' => [
                'content' => 'Hello ',
                'link' => null,
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'Hello ',
            'href' => null,
        ],
        [
            'type' => 'text',
            'text' => [
                'content' => 'world',
                'link' => null,
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'world',
            'href' => null,
        ],
    ];

    $richText = new RichText($data);

    // Test string conversion
    expect((string) $richText)->toBe('Hello world');

    // Test setPlainText
    $richText->setPlainText('New text');
    expect($richText->getPlainText())->toBe('New text');
    expect($richText->getItems())->toHaveCount(1);
});

/** @test */
it('gets all types', function () {
    $data = [
        [
            'type' => 'text',
            'text' => [
                'content' => 'Text',
                'link' => null,
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'Text',
            'href' => null,
        ],
        [
            'type' => 'mention',
            'mention' => [
                'type' => 'user',
                'user' => [
                    'object' => 'user',
                    'id' => 'b2e19928-b427-4aad-9a9d-fde65479b1d9',
                ],
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => '@User',
            'href' => null,
        ],
        [
            'type' => 'equation',
            'equation' => [
                'expression' => 'E = mc^2',
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'E = mc^2',
            'href' => null,
        ],
    ];

    $richText = new RichText($data);
    $types = $richText->getTypes();

    expect($types)->toHaveCount(3);
    expect($types->contains('text'))->toBeTrue();
    expect($types->contains('mention'))->toBeTrue();
    expect($types->contains('equation'))->toBeTrue();
});

/** @test */
it('detects links', function () {
    $dataWithLink = [
        [
            'type' => 'text',
            'text' => [
                'content' => 'click here',
                'link' => [
                    'url' => 'https://example.com',
                ],
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'click here',
            'href' => 'https://example.com',
        ],
    ];

    $dataWithoutLink = [
        [
            'type' => 'text',
            'text' => [
                'content' => 'Plain text',
                'link' => null,
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'Plain text',
            'href' => null,
        ],
    ];

    expect((new RichText($dataWithLink))->hasLinks())->toBeTrue();
    expect((new RichText($dataWithoutLink))->hasLinks())->toBeFalse();
});

/** @test */
it('gets linked items', function () {
    $data = [
        [
            'type' => 'text',
            'text' => [
                'content' => 'Link 1',
                'link' => [
                    'url' => 'https://example1.com',
                ],
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'Link 1',
            'href' => 'https://example1.com',
        ],
        [
            'type' => 'text',
            'text' => [
                'content' => 'No link',
                'link' => null,
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'No link',
            'href' => null,
        ],
        [
            'type' => 'text',
            'text' => [
                'content' => 'Link 2',
                'link' => [
                    'url' => 'https://example2.com',
                ],
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'Link 2',
            'href' => 'https://example2.com',
        ],
    ];

    $richText = new RichText($data);
    $linkedItems = $richText->getLinkedItems();

    expect($linkedItems)->toHaveCount(2);

    /** @var RichTextItem|null $firstItem */
    $firstItem = $linkedItems->first();
    /** @var RichTextItem|null $lastItem */
    $lastItem = $linkedItems->last();

    expect($firstItem)->not->toBeNull();
    expect($lastItem)->not->toBeNull();

    if ($firstItem !== null && $lastItem !== null) {
        expect($firstItem->getHref())->toBe('https://example1.com');
        expect($lastItem->getHref())->toBe('https://example2.com');
    }
});

/** @test */
it('detects annotations', function () {
    $dataWithAnnotations = [
        [
            'type' => 'text',
            'text' => [
                'content' => 'Bold text',
                'link' => null,
            ],
            'annotations' => [
                'bold' => true,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'Bold text',
            'href' => null,
        ],
    ];

    $dataWithoutAnnotations = [
        [
            'type' => 'text',
            'text' => [
                'content' => 'Plain text',
                'link' => null,
            ],
            'annotations' => [
                'bold' => false,
                'italic' => false,
                'strikethrough' => false,
                'underline' => false,
                'code' => false,
                'color' => 'default',
            ],
            'plain_text' => 'Plain text',
            'href' => null,
        ],
    ];

    expect((new RichText($dataWithAnnotations))->hasAnnotations())->toBeTrue();
    expect((new RichText($dataWithoutAnnotations))->hasAnnotations())->toBeFalse();
});



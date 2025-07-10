<?php

declare(strict_types=1);

use Honed\Honed\Responses\IndexResponse;

beforeEach(function () {
    $this->response = new IndexResponse();
});

it('merges props', function () {
    expect($this->response)
        ->getProps()->toBe([
            IndexResponse::TITLE_PROP => null,
            IndexResponse::HEAD_PROP => null,
        ])
        ->props(['key' => 'value'])->toBe($this->response)
        ->getProps()->toEqualCanonicalizing([
            IndexResponse::TITLE_PROP => null,
            IndexResponse::HEAD_PROP => null,
            'key' => 'value',
        ]);
});

it('adds props', function () {
    expect($this->response)
        ->getProps()->toBe([
            IndexResponse::TITLE_PROP => null,
            IndexResponse::HEAD_PROP => null,
        ])
        ->props('key', 'value')->toBe($this->response)
        ->getProps()->toEqualCanonicalizing([
            IndexResponse::TITLE_PROP => null,
            IndexResponse::HEAD_PROP => null,
            'key' => 'value',
        ])
        ->prop('key2', 'value2')->toBe($this->response)
        ->getProps()->toEqualCanonicalizing([
            IndexResponse::TITLE_PROP => null,
            IndexResponse::HEAD_PROP => null,
            'key' => 'value',
            'key2' => 'value2',
        ]);
});

<?php

declare(strict_types=1);

use Honed\Action\Concerns\HandlesBulkActions;
use Honed\Action\Contracts\ShouldChunk;

beforeEach(function () {
    $this->test = new class()
    {
        use HandlesBulkActions;
    };
});

it('chunks', function () {
    expect($this->test)
        ->isChunked()->toBe(config('action.chunk'))
        ->chunks(true)->toBe($this->test)
        ->isChunked()->toBeTrue()
        ->isChunkedByDefault()->toBe(config('action.chunk'));

    $test = new class() implements ShouldChunk
    {
        use HandlesBulkActions;
    };

    expect($test)
        ->isChunked()->toBeTrue();
});

it('chunks by id', function () {
    expect($this->test)
        ->isChunkedById()->toBe(config('action.chunk_by_id'))
        ->chunksById(true)->toBe($this->test)
        ->isChunkedById()->toBeTrue()
        ->isChunkedByIdByDefault()->toBe(config('action.chunk_by_id'));
});

it('has chunk size', function () {
    expect($this->test)
        ->getChunkSize()->toBe(config('action.chunk_size'))
        ->chunkSize(10)->toBe($this->test)
        ->getChunkSize()->toBe(10)
        ->getDefaultChunkSize()->toBe(config('action.chunk_size'));
});

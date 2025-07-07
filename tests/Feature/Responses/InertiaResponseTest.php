<?php

declare(strict_types=1);

use Honed\Honed\Responses\InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->response = new InertiaResponse();
});

it('has title', function () {
    expect($this->response)
        ->getTitle()->toBeNull()
        ->title('Title')->toBe($this->response)
        ->getTitle()->toBe('Title');
});

it('has head', function () {
    expect($this->response)
        ->getHead()->toBeNull()
        ->title('Title')->toBe($this->response)
        ->getHead()->toBe('Title')
        ->head('Head')->toBe($this->response)
        ->getHead()->toBe('Head');
});

it('has page', function () {
    expect($this->response)
        ->getPage()->toBeNull()
        ->page('Page')->toBe($this->response)
        ->getPage()->toBe('Page');
});

it('has modal', function () {
    expect($this->response)
        ->getModal()->toBeNull()
        ->modal('Modal')->toBe($this->response)
        ->getModal()->toBe('Modal');
});

it('has base url', function () {
    expect($this->response)
        ->getBaseUrl()->toBeNull()
        ->base('Base')->toBe($this->response)
        ->getBaseUrl()->toBe('Base');
});

it('errors if no renderable input', function () {
    $this->response->toResponse(request());
})->throws(RuntimeException::class);

it('renders page', function () {
    expect($this->response)
        ->page('Page')
        ->toResponse(request())->toBeInstanceOf(Response::class);
});

it('errors if no base is set for modal', function () {
    $this->response->modal('Modal')->toResponse(request());
})->throws(RuntimeException::class);

it('renders modal', function () {
    expect($this->response)
        ->modal('Modal')
        ->base('Base')
        ->toResponse(request())->toBeInstanceOf(Response::class);
});

it('is macroable', function () {
    $this->response->macro('test', fn () => 'test');

    expect($this->response->test())->toBe('test');
});

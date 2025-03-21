<?php

declare(strict_types=1);

use Honed\Core\Concerns\HasScope;

beforeEach(function () {
    $this->test = new class {
        use HasScope;
    };
    $this->param = 'scope';
});

it('is null by default', function () {
    expect($this->test)
        ->hasScope()->toBeFalse();
});

it('sets', function () {
    expect($this->test->scope($this->param))
        ->toBe($this->test)
        ->hasScope()->toBeTrue();
});

it('gets', function () {
    expect($this->test->scope($this->param))
        ->getScope()->toBe($this->param)
        ->hasScope()->toBeTrue();
});

it('formats', function () {
    expect($this->test)
        ->formatScope('test')->toBe('test')
        ->hasScope()->toBeFalse();

    expect($this->test->scope($this->param))
        ->formatScope('test')->toBe('scope:test')
        ->hasScope()->toBeTrue();
});

it('decodes', function () {
    expect($this->test->scope($this->param))
        ->decodeScope('scope:test')->toBe('test');
});

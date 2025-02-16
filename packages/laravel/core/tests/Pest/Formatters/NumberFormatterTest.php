<?php

use Honed\Core\Formatters\NumberFormatter;
use Illuminate\Support\Number;

beforeEach(function () {
    $this->formatter = NumberFormatter::make();
});

it('makes', function () {
    expect(NumberFormatter::make())
        ->toBeInstanceOf(NumberFormatter::class);
});

it('sets precision', function () {
    expect($this->formatter->precision(2))
        ->toBeInstanceOf(NumberFormatter::class)
        ->getPrecision()->toBe(2)
        ->hasPrecision()->toBeTrue();
});

it('sets divide by', function () {
    expect($this->formatter->divideBy(1000))
        ->toBeInstanceOf(NumberFormatter::class)
        ->getDivideBy()->toBe(1000)
        ->hasDivideBy()->toBeTrue();
});

it('sets locale', function () {
    expect($this->formatter->locale('US'))
        ->toBeInstanceOf(NumberFormatter::class)
        ->getLocale()->toBe('US')
        ->hasLocale()->toBeTrue();
});

it('sets currency', function () {
    expect($this->formatter->currency('USD'))
        ->toBeInstanceOf(NumberFormatter::class)
        ->getCurrency()->toBe('USD')
        ->hasCurrency()->toBeTrue();
});

it('sets cents', function () {
    expect($this->formatter->cents())
        ->toBeInstanceOf(NumberFormatter::class)
        ->getDivideBy()->toBe(100)
        ->hasDivideBy()->toBeTrue();
});

it('formats', function () {
    expect($this->formatter->format(1000))
        ->toBe(1000);

    expect($this->formatter->divideBy(100)->format(1000))
        ->toBe(10);

    expect($this->formatter->locale('US')->format(1000))
        ->toBe(Number::format(10, locale: 'US'));

    expect($this->formatter->currency('USD')->format(1000))
        ->toBe(Number::currency(10, 'USD', 'US'));

    expect($this->formatter->format(null))
        ->toBeNull();

    expect($this->formatter->format('testing'))
        ->toBeNull();
});

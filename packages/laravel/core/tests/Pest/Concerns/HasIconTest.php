<?php

use Honed\Core\Concerns\HasIcon;

class HasIconComponent
{
    use HasIcon;
}

beforeEach(function () {
    $this->component = new HasIconComponent;
});

it('has no icon by default', function () {
    expect($this->component)
        ->getIcon()->toBeNull()
        ->hasIcon()->toBeFalse();
});

it('sets icon', function () {
    $this->component->setIcon('Icon');
    expect($this->component)
        ->getIcon()->toBe('Icon')
        ->hasIcon()->toBeTrue();
});

it('rejects null values', function () {
    $this->component->setIcon('Icon');
    $this->component->setIcon(null);
    expect($this->component)
        ->getIcon()->toBe('Icon')
        ->hasIcon()->toBeTrue();
});

it('chains icon', function () {
    expect($this->component->icon('Icon'))->toBeInstanceOf(HasIconComponent::class)
        ->getIcon()->toBe('Icon')
        ->hasIcon()->toBeTrue();
});
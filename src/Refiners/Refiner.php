<?php

namespace Jdw5\Vanguard\Refiners;

use Closure;
use Conquest\Core\Primitive;
use Conquest\Core\Concerns\HasName;
use Conquest\Core\Concerns\HasType;
use Conquest\Core\Concerns\HasLabel;
use Conquest\Core\Concerns\IsActive;
use Conquest\Core\Concerns\HasMetadata;
use Conquest\Core\Concerns\HasProperty;
use Conquest\Core\Concerns\HasAuthorization;

abstract class Refiner extends Primitive
{
    use HasProperty;
    use HasName;
    use HasLabel;
    use HasMetadata;
    use HasType;
    use HasAuthorization;
    use IsActive;

    public function __construct(
        string|Closure $property, 
        string|Closure $name = null,
        string|Closure $label = null,
        bool|Closure $authorize = null, 
    ) {
        $this->setProperty($property);
        $this->setName($name ?? $this->toName($property));
        $this->setLabel($label ?? $this->toLabel($this->getName()));
        $this->setAuthorize($authorize);
    }

    /**
     * Convert the refinement to an array representation
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'metadata' => $this->getMetadata(),
            'active' => $this->isActive(),
        ];
    }
    
    /**
     * Serialise the refinement to JSON
     * 
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
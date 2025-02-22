<?php

declare(strict_types=1);

namespace Honed\Nav;

use Honed\Core\Concerns\Allowable;
use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasRequest;
use Honed\Core\Primitive;
use Illuminate\Http\Request;

/**
 * @extends Primitive<string, mixed>
 */
abstract class NavBase extends Primitive
{
    use Allowable;
    use HasIcon;
    use HasLabel;
    use HasRequest;

    public function __construct(Request $request)
    {
        $this->request($request);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'label' => $this->getLabel(),
            'icon' => $this->getIcon(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        $this->request = request();

        return $this->resolveRequestClosureDependencyForEvaluationByName($parameterName);
    }

    /**
     * {@inheritDoc}
     */
    public function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $this->request = request();

        return $this->resolveRequestClosureDependencyForEvaluationByType($parameterType);
    }
}

<?php

declare(strict_types=1);

namespace Honed\Honed\Responses;

use BadMethodCallException;
use Honed\Honed\Responses\Concerns\HasProps;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Inertia\Inertia;
use RuntimeException;

class InertiaResponse implements Responsable
{
    use Conditionable;
    use HasProps {
        getProps as baseProps;
    }
    use Macroable {
        __call as macroCall;
    }

    public const TITLE_PROP = 'title';

    public const HEAD_PROP = 'head';

    /**
     * The title for the page.
     *
     * @var string|null
     */
    protected $title;

    /**
     * The head of the page.
     *
     * @var string|null
     */
    protected $head;

    /**
     * The page to be rendered.
     *
     * @var string|null
     */
    protected $page;

    /**
     * The modal to be rendered.
     *
     * @var string|null
     */
    protected $modal;

    /**
     * The base url to be used for modals.
     *
     * @var string|null
     */
    protected $base;

    /**
     * Handle dynamic method calls into the instance.
     *
     * @param  string  $method
     * @param  array<int,mixed>  $parameters
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        return $this->macroCall($method, $parameters);
    }

    /**
     * Provide the instance with any necessary setup.
     */
    protected function setUp(): void
    {
        //
    }

    /**
     * Set the title for the page.
     *
     * @return $this
     */
    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the title of the page.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the head of the page.
     *
     * @return $this
     */
    public function head(string $head): static
    {
        $this->head = $head;

        return $this;
    }

    /**
     * Get the head of the page.
     */
    public function getHead(): ?string
    {
        return $this->head ??= $this->getTitle();
    }

    /**
     * The page to be rendered.
     *
     * @return $this
     */
    public function page(string $page): static
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get the page to be rendered.
     */
    public function getPage(): ?string
    {
        return $this->page;
    }

    /**
     * The modal to be rendered.
     *
     * @return $this
     */
    public function modal(string $modal): static
    {
        $this->modal = $modal;

        return $this;
    }

    /**
     * Get the modal to be rendered.
     */
    public function getModal(): ?string
    {
        return $this->modal;
    }

    /**
     * Set the base url to be used for modals.
     *
     * @return $this
     */
    public function base(string $base): static
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Get the base url to be used for modals.
     */
    public function getBaseUrl(): ?string
    {
        return $this->base;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $this->setUp();

        $this->definition();

        return $this->render()->toResponse($request);
    }

    /**
     * Generate the props for the view.
     *
     * @return array<string, mixed>
     */
    public function getProps(): array
    {
        return [
            self::TITLE_PROP => $this->getTitle(),
            self::HEAD_PROP => $this->getHead(),
            ...$this->baseProps(),
        ];
    }

    /**
     * Define the response.
     *
     * @return $this
     */
    protected function definition(): static
    {
        return $this;
    }

    /**
     * Render the page or modal.
     *
     *
     * @throws RuntimeException
     */
    protected function render(): Responsable
    {
        return match (true) {
            (bool) ($page = $this->getPage()) => $this->renderPage($page),
            (bool) ($modal = $this->getModal()) => $this->renderModal($modal),
            default => throw new RuntimeException(
                'No page or modal was set, the Inertia response cannot be rendered.'
            )
        };
    }

    /**
     * Render the page.
     *
     * @return \Inertia\Response
     */
    protected function renderPage(string $page): Responsable
    {
        return Inertia::render($page, $this->toProps());
    }

    /**
     * Render the modal.
     *
     * @return \Inertia\Response
     */
    protected function renderModal(string $modal): Responsable
    {
        $base = $this->getBaseUrl();

        if (! $base) {
            throw new RuntimeException(
                'Modals require a base url to be set.'
            );
        }

        return Inertia::render($modal, $this->toProps());
    }
}

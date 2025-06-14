<?php

declare(strict_types=1);

namespace Honed\Action;

use Closure;
use Honed\Core\Primitive;

class Confirm extends Primitive
{
    public const CONSTRUCTIVE = 'constructive';

    public const DESTRUCTIVE = 'destructive';

    public const INFORMATIVE = 'informative';

    /**
     * The title of the confirm.
     *
     * @var string|Closure(mixed...):string|null
     */
    protected $title;

    /**
     * The description of the confirm
     *
     * @var string|Closure(mixed...):string|null
     */
    protected $description;

    /**
     * The intent of the confirm.
     *
     * @var string|null
     */
    protected $intent;

    /**
     * The message to display on the submit button.
     *
     * @var string|null
     */
    protected $submit;

    /**
     * The default message to display on the submit button.
     *
     * @var string
     */
    protected static $useSubmit = 'Confirm';

    /**
     * The message to display on the dismiss button.
     *
     * @var string|null
     */
    protected $dismiss;

    /**
     * The default message to display on the dismiss button.
     *
     * @var string
     */
    protected static $useDismiss = 'Cancel';

    /**
     * Create a new confirm instance.
     *
     * @param  string|Closure(mixed...):string|null  $title
     * @param  string|Closure(mixed...):string|null  $description
     * @return static
     */
    public static function make($title = null, $description = null)
    {
        return resolve(static::class)
            ->title($title)
            ->description($description);
    }

    /**
     * Set the default submit message for the confirm.
     *
     * @param  string  $submit
     * @return void
     */
    public static function useSubmit($submit)
    {
        static::$useSubmit = $submit;
    }

    /**
     * Set the default dismiss message for the confirm.
     *
     * @param  string  $dismiss
     * @return void
     */
    public static function useDismiss($dismiss)
    {
        static::$useDismiss = $dismiss;
    }

    /**
     * Set the title of the confirm.
     *
     * @param  string|Closure(mixed...):string|null  $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the title of the confirm.
     *
     * @param  array<string, mixed>  $parameters
     * @param  array<class-string, mixed>  $typed
     * @return string|null
     */
    public function getTitle($parameters = [], $typed = [])
    {
        return $this->evaluate($this->title, $parameters, $typed);
    }

    /**
     * Set the description of the confirm.
     *
     * @param  string|Closure(mixed...):string|null  $description
     * @return $this
     */
    public function description($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description of the confirm.
     *
     * @param  array<string, mixed>  $parameters
     * @param  array<class-string, mixed>  $typed
     * @return string|null
     */
    public function getDescription($parameters = [], $typed = [])
    {
        return $this->evaluate($this->description, $parameters, $typed);
    }

    /**
     * Set the intent of the confirm.
     *
     * @param  string|null  $intent
     * @return $this
     */
    public function intent($intent)
    {
        $this->intent = $intent;

        return $this;
    }

    /**
     * Set the intent as constructive.
     *
     * @return $this
     */
    public function constructive()
    {
        return $this->intent(self::CONSTRUCTIVE);
    }

    /**
     * Set the intent as destructive.
     *
     * @return $this
     */
    public function destructive()
    {
        return $this->intent(self::DESTRUCTIVE);
    }

    /**
     * Set the intent as informative.
     *
     * @return $this
     */
    public function informative()
    {
        return $this->intent(self::INFORMATIVE);
    }

    /**
     * Get the intent of the confirm.
     *
     * @return string|null
     */
    public function getIntent()
    {
        return $this->intent;
    }

    /**
     * Set the submit message for the confirm.
     *
     * @param  string|null  $submit
     * @return $this
     */
    public function submit($submit)
    {
        $this->submit = $submit;

        return $this;
    }

    /**
     * Get the submit message for the confirm.
     *
     * @return string
     */
    public function getSubmit()
    {
        return $this->submit ?? static::$useSubmit;
    }

    /**
     * Set the dismiss message for the confirm.
     *
     * @param  string|null  $dismiss
     * @return $this
     */
    public function dismiss($dismiss)
    {
        $this->dismiss = $dismiss;

        return $this;
    }

    /**
     * Get the dismiss message for the confirm.
     *
     * @return string
     */
    public function getDismiss()
    {
        return $this->dismiss ?? static::$useDismiss;
    }

    /**
     * Flush the global configuration state.
     * 
     * @return void
     */
    public static function flushState()
    {
        static::$useSubmit = 'Confirm';
        static::$useDismiss = 'Cancel';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($named = [], $typed = [])
    {
        return [
            'title' => $this->getTitle($named, $typed),
            'description' => $this->getDescription($named, $typed),
            'dismiss' => $this->getDismiss(),
            'submit' => $this->getSubmit(),
            'intent' => $this->getIntent(),
        ];
    }
}

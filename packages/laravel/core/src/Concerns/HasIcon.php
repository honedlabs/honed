<?php

declare(strict_types=1);

namespace Honed\Core\Concerns;

use Honed\Core\Contracts\IsIcon;

trait HasIcon
{
    /**
     * @var string|\Honed\Core\Contracts\IsIcon
     */
    protected $icon;

    /**
     * Get or set the icon for the instance.
     * 
     * @param string|\Honed\Core\Contracts\IsIcon|null $icon The icon to set, or null to retrieve the current icon.
     * @return string|null|$this The current icon when no argument is provided, or the instance when setting the icon.
     */
    public function icon($icon = null)
    {
        if (\is_null($icon)) {
            return $this->icon instanceof IsIcon ? $this->icon->icon() : $this->icon;
        }

        $this->icon = $icon;

        return $this;
    }

    /**
     * Determine if the instance has an icon set.
     * 
     * @return bool True if an icon is set, false otherwise.
     */
    public function hasIcon()
    {
        return ! \is_null($this->icon);
    }
}

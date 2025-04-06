<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\HasBulkActions;

class BulkAction extends Action
{
    use HasBulkActions;

    /**
     * Whether the action keeps the records selected after successful execution.
     *
     * @var bool
     */
    protected $keepSelected = false;

    /**
     * {@inheritdoc}
     * 
     * @return string
     */
    public function defineType()
    {
        return ActionFactory::BULK;
    }

    /**
     * Set the action to keep the records selected.
     *
     * @param  bool  $keep
     * @return $this
     */
    public function keepSelected($keep = true)
    {
        $this->keepSelected = $keep;

        return $this;
    }

    /**
     * Determine if the action keeps the records selected.
     *
     * @return bool
     */
    public function keepsSelected()
    {
        return $this->keepSelected;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return \array_merge(parent::toArray(), [
            'keepSelected' => $this->keepsSelected(),
        ]);
    }
}

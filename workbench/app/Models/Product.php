<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Honed\Action\Attributes\UseBatch;
use Honed\Honed\Attributes\UseData;
use Honed\Honed\Concerns\Authorable;
use Honed\Honed\Concerns\IsHoned;
use Honed\Infolist\Attributes\UseInfolist;
use Honed\Stats\Attributes\UseOverview;
use Honed\Table\Attributes\UseTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Workbench\App\Batches\ProductBatch;
use Workbench\App\Builders\ProductBuilder;
use Workbench\App\Data\ProductData;
use Workbench\App\Enums\Status;
use Workbench\App\Infolists\ProductInfolist;
use Workbench\App\Overviews\ProductOverview;
use Workbench\App\Tables\ProductTable;
use Workbench\Database\Factories\ProductFactory;

#[UseTable(ProductTable::class)]
#[UseInfolist(ProductInfolist::class)]
#[UseBatch(ProductBatch::class)]
#[UseOverview(ProductOverview::class)]
#[UseData(ProductData::class)]
class Product extends Model
{
    use Authorable;

    /**
     * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Workbench\Database\Factories\ProductFactory>
     */
    use HasFactory;

    use IsHoned;
    use SoftDeletes;

    /**
     * The factory for the model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Factories\Factory>
     */
    protected static $factory = ProductFactory::class;

    /**
     * The attributes that cannot be mass assigned.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string>
     */
    protected $casts = [
        'status' => Status::class,
    ];

    /**
     * Begin querying the model.
     *
     * @return ProductBuilder<$this>
     */
    public static function query()
    {
        /** @var ProductBuilder<$this> */
        return parent::query();
    }

    /**
     * Get the user that owns the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @return ProductBuilder<$this>
     */
    public function newEloquentBuilder($query)
    {
        return new ProductBuilder($query);
    }

    /**
     * Get the search data for the model using Laravel Scout.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
        ];
    }
}

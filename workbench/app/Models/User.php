<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Honed\Honed\Concerns\Transferable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Workbench\App\Data\IdData;
use Workbench\Database\Factories\UserFactory;

class User extends Authenticatable
{
    /**
     * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Workbench\Workbench\Database\Factories\UserFactory>
     */
    use HasFactory;

    use Notifiable;

    use Transferable;

    protected $dataClass = IdData::class;

    /**
     * The factory for the model.
     *
     * @return class-string<\Illuminate\Database\Eloquent\Factories\Factory>
     */
    protected static $factory = UserFactory::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Remove the data class from the model.
     * 
     * @internal
     */
    public function removeDataClass(): void
    {
        unset($this->dataClass);
    }
}

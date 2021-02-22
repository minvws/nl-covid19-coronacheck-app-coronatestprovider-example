<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\TestType
 *
 *
 * TestTypes available for use by the CoronaCheck app.
 *
 * @property string $uuid
 * @property string $name
 * @method static Builder|TestType newModelQuery()
 * @method static Builder|TestType newQuery()
 * @method static Builder|TestType query()
 * @method static Builder|TestType whereId($value)
 * @mixin Eloquent
 */


class TestType extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    public $timestamps = false;

    /** @var string */
    public $table = 'TestType';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
    ];


}

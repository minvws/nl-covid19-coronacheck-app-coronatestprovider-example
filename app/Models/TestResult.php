<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ramsey\Uuid\Uuid;

/**
 * App\Models\TestResult
 *
 *
 * Token is used to gather data by the CoronaCheck app.
 *
 * @property string $uuid
 * @property string $token
 * @property string $testTypeId
 * @property string $sampleDate
 * @property string $result
 * @property string $birthDate
 * @property string $verificationCode
 * @method static Builder|TestResult newModelQuery()
 * @method static Builder|TestResult newQuery()
 * @method static Builder|TestResult query()
 * @method static Builder|TestResult whereId($value)
 * @mixin Eloquent
 */


class TestResult extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    public $timestamps = false;

    /** @var string */
    public $table = 'TestResults';

    protected $primaryKey = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'token',
        'status',
        'sampleDate',
        'testTypeId',
        'result',
        'birthDate',
        'verificationCode',
        'fetchedCount'
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(
            function (TestResult $tr) {
                $tr->uuid = (string)Uuid::uuid4();
            }
        );
    }

}

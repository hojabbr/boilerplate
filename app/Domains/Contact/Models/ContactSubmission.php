<?php

namespace App\Domains\Contact\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperContactSubmission
 */
class ContactSubmission extends Model
{
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'locale',
    ];

    protected static function booted(): void
    {
        static::creating(function (ContactSubmission $submission) {
            if (empty($submission->locale)) {
                $submission->locale = app()->getLocale();
            }
        });
    }
}

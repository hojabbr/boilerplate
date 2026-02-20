<?php

namespace App\Domains\Contact\Actions;

use App\Domains\Contact\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Laravel\Pennant\Feature;

class StoreContactSubmission
{
    /**
     * @param  array{name: string, email: string, subject?: string, message: string}  $data
     */
    public function handle(array $data): RedirectResponse
    {
        if (! Feature::active('contact-form')) {
            abort(404);
        }

        ContactSubmission::create($data);

        return Redirect::back()->with('success', __('contact.message_sent'));
    }
}

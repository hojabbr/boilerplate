<?php

use App\Domains\Contact\Actions\StoreContactSubmission;
use Laravel\Pennant\Feature;

test('store contact submission creates record and returns redirect with success', function () {
    Feature::activate('contact-form');

    $action = app(StoreContactSubmission::class);
    $response = $action->handle([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Inquiry',
        'message' => 'Hello, I have a question.',
    ]);

    expect($response->getStatusCode())->toBe(302);
    $this->assertDatabaseHas('contact_submissions', [
        'email' => 'john@example.com',
        'name' => 'John Doe',
    ]);
});

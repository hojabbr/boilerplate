<?php

test('contact form submission creates record and redirects with success', function () {
    $response = $this->post('/en/contact', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'subject' => 'Test',
        'message' => 'Hello, this is a test message.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('contact_submissions', [
        'email' => 'jane@example.com',
        'name' => 'Jane Doe',
    ]);
});

test('contact form validates required fields', function () {
    $response = $this->post('/en/contact', [
        'name' => '',
        'email' => 'invalid',
        'message' => '',
    ]);

    $response->assertSessionHasErrors(['name', 'email', 'message']);
});

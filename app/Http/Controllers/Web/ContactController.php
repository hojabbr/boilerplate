<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactSubmissionRequest;
use App\Models\ContactSubmission;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Pennant\Feature;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ContactController extends Controller
{
    public function show(Request $request): Response|HttpResponse
    {
        if (! Feature::active('contact-form')) {
            abort(404);
        }

        $setting = Setting::site();
        $socialLinksRaw = $setting->getAttribute('social_links');
        /** @var array<string, mixed> $socialLinks */
        $socialLinks = is_array($socialLinksRaw) ? array_filter($socialLinksRaw) : [];

        return Inertia::render('contact/Show', [
            'settings' => [
                'company_name' => $setting->company_name,
                'tagline' => $setting->tagline,
                'email' => $setting->email,
                'phone' => $setting->phone,
                'social_links' => $socialLinks,
            ],
            'features' => [
                'pages' => Feature::active('pages'),
                'blog' => Feature::active('blog'),
                'contactForm' => Feature::active('contact-form'),
            ],
            'contactStoreUrl' => route('contact.store'),
            'success' => session('success'),
            'seo' => [
                'title' => __('Contact').' - '.($setting->company_name ?: config('app.name')),
                'description' => $setting->tagline ?: config('app.description') ?: __('Get in touch with us.'),
            ],
            'messages' => [
                'heading' => __('contact.heading'),
                'form_name' => __('contact.form.name'),
                'form_email' => __('contact.form.email'),
                'form_subject' => __('contact.form.subject'),
                'form_message' => __('contact.form.message'),
                'form_send' => __('contact.form.send'),
                'label_email' => __('contact.label_email'),
                'label_phone' => __('contact.label_phone'),
                'label_social' => __('contact.label_social'),
            ],
        ]);
    }

    public function store(StoreContactSubmissionRequest $request): RedirectResponse
    {
        if (! Feature::active('contact-form')) {
            abort(404);
        }

        ContactSubmission::create($request->validated());

        return redirect()->back()->with('success', __('contact.message_sent'));
    }
}

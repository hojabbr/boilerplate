<?php

namespace App\Domains\Contact\Http\Controllers;

use App\Core\Contracts\PagePropsServiceInterface;
use App\Core\Models\Setting;
use App\Domains\Contact\Actions\StoreContactSubmission;
use App\Domains\Contact\Http\Requests\StoreContactSubmissionRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Pennant\Feature;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ContactController extends Controller
{
    public function show(Request $request, PagePropsServiceInterface $pageProps): Response|HttpResponse
    {
        if (! Feature::active('contact-form')) {
            abort(404);
        }

        $setting = Setting::site();
        $settings = $pageProps->settingsSlice($setting);

        return Inertia::render('contact/Show', [
            'settings' => $settings,
            'features' => $pageProps->featuresArray(),
            'contactStoreUrl' => route('contact.store'),
            'success' => session('success'),
            'seo' => [
                'title' => __('Contact').' - '.$settings['company_name'],
                'description' => $settings['tagline'] ?: __('Get in touch with us.'),
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

    public function store(StoreContactSubmissionRequest $request, StoreContactSubmission $action): RedirectResponse
    {
        return $action->handle($request->validatedData());
    }
}

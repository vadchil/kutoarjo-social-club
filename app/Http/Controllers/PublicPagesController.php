<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Gallery;
use App\Models\PricingRule;
use App\Models\SiteSetting;
use Inertia\Inertia;
use Inertia\Response;

class PublicPagesController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Public/Home', $this->shared());
    }

    public function padel(): Response
    {
        return Inertia::render('Public/Padel', $this->shared());
    }

    public function billiard(): Response
    {
        return Inertia::render('Public/Billiard', $this->shared());
    }

    public function gallery(): Response
    {
        return Inertia::render('Public/Gallery', $this->shared() + [
            'items' => Gallery::where('is_published', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function faq(): Response
    {
        return Inertia::render('Public/FAQ', $this->shared() + [
            'faqs' => Faq::where('is_published', true)->orderBy('category')->orderBy('sort_order')->get(),
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('Public/Contact', $this->shared());
    }

    private function shared(): array
    {
        $allowed = [
            'business_name', 'business_description', 'whatsapp_number', 'instagram_url',
            'ayo_booking_url', 'google_maps_url', 'business_address', 'operational_hours',
        ];

        $settings = SiteSetting::whereIn('key', $allowed)->where('is_public', true)->get()
            ->mapWithKeys(fn (SiteSetting $setting) => [$setting->key => $setting->value['value'] ?? null]);

        $prices = PricingRule::where('is_active', true)->orderByDesc('effective_from')->get()
            ->unique(fn (PricingRule $rule) => $rule->activity_type.'-'.$rule->day_type)
            ->mapWithKeys(fn (PricingRule $rule) => [
                $rule->activity_type.'_'.$rule->day_type => $rule->price_per_hour,
            ]);

        return ['settings' => $settings, 'prices' => $prices];
    }
}

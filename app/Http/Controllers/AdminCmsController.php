<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Gallery;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminCmsController extends Controller
{
    public function galleryIndex(): Response
    {
        return Inertia::render('Admin/Cms/Gallery', ['items' => Gallery::orderBy('sort_order')->get()]);
    }

    public function galleryStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:padel,billiard,venue'],
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'alt_text' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $extension = $request->file('image')->guessExtension();
        $path = $request->file('image')->storeAs('galleries', Str::ulid().'.'.$extension, 'public');

        Gallery::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'image_path' => $path,
            'alt_text' => $validated['alt_text'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_published' => true,
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Gallery item created.');
    }

    public function galleryUpdate(Request $request, Gallery $gallery): RedirectResponse
    {
        $gallery->update($request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:padel,billiard,venue'],
            'alt_text' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_published' => ['required', 'boolean'],
        ]));

        return back()->with('success', 'Gallery item updated.');
    }

    public function galleryDestroy(Gallery $gallery): RedirectResponse
    {
        Storage::disk('public')->delete($gallery->image_path);
        $gallery->delete();

        return back()->with('success', 'Gallery item deleted.');
    }

    public function faqIndex(): Response
    {
        return Inertia::render('Admin/Cms/Faq', ['faqs' => Faq::orderBy('category')->orderBy('sort_order')->get()]);
    }

    public function faqStore(Request $request): RedirectResponse
    {
        Faq::create($request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'category' => ['required', 'in:general,padel,billiard'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]) + ['is_published' => true, 'created_by' => $request->user()->id]);

        return back()->with('success', 'FAQ created.');
    }

    public function faqUpdate(Request $request, Faq $faq): RedirectResponse
    {
        $faq->update($request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'category' => ['required', 'in:general,padel,billiard'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_published' => ['required', 'boolean'],
        ]));

        return back()->with('success', 'FAQ updated.');
    }

    public function faqDestroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return back()->with('success', 'FAQ deleted.');
    }

    public function settingsIndex(): Response
    {
        return Inertia::render('Admin/Cms/Settings', ['settings' => SiteSetting::orderBy('key')->get()]);
    }

    public function settingsUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate(['settings' => ['required', 'array']]);

        foreach ($validated['settings'] as $key => $value) {
            SiteSetting::where('key', $key)->update([
                'value' => ['name' => Str::headline($key), 'value' => $value],
                'updated_by' => $request->user()->id,
            ]);
        }

        return back()->with('success', 'Settings updated.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    public function confirm(ContactRequest $request)
{
    $validated = $request->validated();

    $category = Category::find($validated['category_id']);

    $tags = Tag::whereIn('id', $validated['tag_ids'] ?? [])->get();

    return view('contact.confirm', [
        'validated' => $validated,
        'category' => $category,
        'tags' => $tags,
    ]);
}
public function store(ContactRequest $request)
{
    $validated = $request->validated();

    $contact = Contact::create([
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'gender' => $validated['gender'],
        'email' => $validated['email'],
        'tel' => $validated['tel'],
        'address' => $validated['address'],
        'building' => $validated['building'] ?? null,
        'category_id' => $validated['category_id'],
        'detail' => $validated['detail'],
    ]);

    $contact->tags()->sync($validated['tag_ids'] ?? []);

    return view('contact.thanks');
}
}
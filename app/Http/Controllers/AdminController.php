<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::with(['category', 'tags']);

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                  ->orWhere('last_name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('gender') && $request->gender != 0) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $contacts = $query->latest()->paginate(7);

        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.index', compact(
            'contacts',
            'categories',
            'tags'
        ));
    }

    public function show($id)
    {
        $contact = Contact::with(['category', 'tags'])->findOrFail($id);

        return view('admin.show', compact('contact'));
    }

    public function destroy($id)
{
    $contact = Contact::findOrFail($id);

    $contact->delete();

    return redirect('/admin');
}

public function export(Request $request)
{
    $query = Contact::with(['category', 'tags']);

    if ($request->filled('keyword')) {
        $keyword = $request->keyword;

        $query->where(function ($q) use ($keyword) {
            $q->where('first_name', 'like', "%{$keyword}%")
              ->orWhere('last_name', 'like', "%{$keyword}%")
              ->orWhere('email', 'like', "%{$keyword}%");
        });
    }

    if ($request->filled('gender') && $request->gender != 0) {
        $query->where('gender', $request->gender);
    }

    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    if ($request->filled('date')) {
        $query->whereDate('created_at', $request->date);
    }

    $contacts = $query->latest()->get();

    $csv = "\xEF\xBB\xBF";
    $csv .= "お名前,性別,メールアドレス,お問い合わせの種類,タグ,お問い合わせ内容\n";

    $genderLabels = [
        1 => '男性',
        2 => '女性',
        3 => 'その他',
    ];

    foreach ($contacts as $contact) {
        $name = $contact->first_name . ' ' . $contact->last_name;
        $gender = $genderLabels[$contact->gender] ?? '';
        $category = $contact->category->content ?? '';
        $tags = $contact->tags->pluck('name')->join(', ');
        $detail = $contact->detail;

        $csv .= '"' . str_replace('"', '""', $name) . '",';
        $csv .= '"' . str_replace('"', '""', $gender) . '",';
        $csv .= '"' . str_replace('"', '""', $contact->email) . '",';
        $csv .= '"' . str_replace('"', '""', $category) . '",';
        $csv .= '"' . str_replace('"', '""', $tags) . '",';
        $csv .= '"' . str_replace('"', '""', $detail) . '"' . "\n";
    }

    return response($csv)
        ->header('Content-Type', 'text/csv; charset=UTF-8')
        ->header(
            'Content-Disposition',
            'attachment; filename="contacts.csv"'
        );
}

}
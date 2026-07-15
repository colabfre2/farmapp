<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feed;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $feeds = Feed::when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))->latest()->get();
        return view('admin.feeds.index', compact('feeds', 'query'));
    }

    public function create()
    {
        return view('admin.feeds.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|string|max:255',
            'unit'           => 'required|string|max:50',
            'stock'          => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'description'    => 'nullable|string',
        ]);

        Feed::create($request->all());

        return redirect()->route('admin.feeds.index')->with('success', 'Pakan berhasil ditambahkan!');
    }

    public function edit(Feed $feed)
    {
        return view('admin.feeds.edit', compact('feed'));
    }

    public function update(Request $request, Feed $feed)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|string|max:255',
            'unit'           => 'required|string|max:50',
            'stock'          => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'description'    => 'nullable|string',
        ]);

        $feed->update($request->all());

        return redirect()->route('admin.feeds.index')->with('success', 'Pakan berhasil diperbarui!');
    }

    public function destroy(Feed $feed)
    {
        $feed->delete();
        return redirect()->route('admin.feeds.index')->with('success', 'Pakan berhasil dihapus!');
    }
}
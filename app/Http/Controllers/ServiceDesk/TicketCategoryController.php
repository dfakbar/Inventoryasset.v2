<?php

namespace App\Http\Controllers\ServiceDesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketCategoryRequest;
use App\Http\Requests\UpdateTicketCategoryRequest;
use App\Models\TicketCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TicketCategoryController extends Controller
{
    public function index(): View
    {
        $this->authorize('ticket.manage');

        $categories = TicketCategory::with('parent')->orderBy('name')->paginate(15);

        return view('service-desk.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $this->authorize('ticket.manage');

        $parents = TicketCategory::whereNull('parent_id')->orderBy('name')->get();

        return view('service-desk.categories.create', compact('parents'));
    }

    public function store(StoreTicketCategoryRequest $request): RedirectResponse
    {
        $this->authorize('ticket.manage');

        DB::beginTransaction();
        try {
            TicketCategory::create($request->validated());
            DB::commit();

            return redirect()
                ->route('sd.categories.index')
                ->with('success', 'Kategori tiket berhasil ditambahkan.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan kategori tiket.', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Gagal menyimpan kategori tiket.');
        }
    }

    public function edit(TicketCategory $category): View
    {
        $this->authorize('ticket.manage');

        $parents = TicketCategory::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->get();

        return view('service-desk.categories.edit', compact('category', 'parents'));
    }

    public function update(UpdateTicketCategoryRequest $request, TicketCategory $category): RedirectResponse
    {
        $this->authorize('ticket.manage');

        DB::beginTransaction();
        try {
            $category->update($request->validated());
            DB::commit();

            return redirect()
                ->route('sd.categories.index')
                ->with('success', 'Kategori tiket berhasil diperbarui.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal update kategori tiket.', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Gagal memperbarui kategori tiket.');
        }
    }

    public function destroy(TicketCategory $category): RedirectResponse
    {
        $this->authorize('ticket.manage');

        if ($category->children()->count() > 0) {
            return back()->with('error', 'Kategori memiliki sub-kategori. Hapus sub-kategori terlebih dahulu.');
        }

        if ($category->tickets()->count() > 0) {
            return back()->with('error', 'Kategori masih memiliki tiket. Non-aktifkan saja.');
        }

        DB::beginTransaction();
        try {
            $category->delete();
            DB::commit();

            return redirect()
                ->route('sd.categories.index')
                ->with('success', 'Kategori tiket berhasil dihapus.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal hapus kategori tiket.', ['error' => $e->getMessage()]);

            return back()->with('error', 'Gagal menghapus kategori tiket.');
        }
    }
}

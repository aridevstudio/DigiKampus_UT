<?php

namespace App\Http\Controllers\Auth;

use App\Models\ForumCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminForumCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $statusFilter = (string) $request->query('status', 'all');

        $query = ForumCategory::query();
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->where('slug', 'like', "%{$search}%");
            });
        }
        if ($statusFilter === 'aktif') {
            $query->where('is_active', true);
        } elseif ($statusFilter === 'nonaktif') {
            $query->where('is_active', false);
        }

        $kategoriPaginated = $query->ordered()->paginate(10)->withQueryString();

        $totalAll = ForumCategory::count();
        $totalAktif = ForumCategory::where('is_active', true)->count();
        $totalNonaktif = ForumCategory::where('is_active', false)->count();
        $nextUrutan = (ForumCategory::max('urutan') ?? 0) + 1;

        return view('Auth.admin.forum-kategori', [
            'kategoriPaginated' => $kategoriPaginated,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'totalAll' => $totalAll,
            'totalAktif' => $totalAktif,
            'totalNonaktif' => $totalNonaktif,
            'nextUrutan' => $nextUrutan,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:120'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:80'],
            'warna' => ['nullable', 'string', 'max:20'],
            'urutan' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable'],
        ]);

        $validator->after(function ($v) use ($request) {
            $nama = trim((string) $request->input('nama'));
            if ($nama !== '' && ForumCategory::where('nama', $nama)->exists()) {
                $v->errors()->add('nama', 'Nama kategori sudah digunakan.');
            }
        });

        if ($validator->fails()) {
            return redirect()->route('admin.forum-kategori')
                ->withErrors($validator)
                ->withInput()
                ->with(['_modal' => 'add']);
        }

        ForumCategory::create([
            'nama' => trim((string) $request->input('nama')),
            'deskripsi' => $request->filled('deskripsi') ? trim((string) $request->input('deskripsi')) : null,
            'icon' => $request->filled('icon') ? trim((string) $request->input('icon')) : 'tag',
            'warna' => $request->filled('warna') ? trim((string) $request->input('warna')) : '#3B82F6',
            'urutan' => (int) ($request->input('urutan') ?? 0),
            'is_active' => $this->resolveActive($request),
        ]);

        return redirect()->route('admin.forum-kategori')->with('success', 'Kategori forum berhasil ditambahkan.');
    }

    public function show(int $id): JsonResponse
    {
        $kategori = ForumCategory::findOrFail($id);

        return response()->json([
            'id_forum_category' => $kategori->id_forum_category,
            'nama' => $kategori->nama,
            'deskripsi' => $kategori->deskripsi,
            'icon' => $kategori->icon,
            'warna' => $kategori->warna,
            'urutan' => $kategori->urutan,
            'is_active' => (bool) $kategori->is_active,
            'slug' => $kategori->slug,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $kategori = ForumCategory::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:120'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:80'],
            'warna' => ['nullable', 'string', 'max:20'],
            'urutan' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable'],
        ]);

        $validator->after(function ($v) use ($request, $kategori) {
            $nama = trim((string) $request->input('nama'));
            if ($nama !== '' && ForumCategory::where('nama', $nama)
                ->where('id_forum_category', '!=', $kategori->id_forum_category)
                ->exists()) {
                $v->errors()->add('nama', 'Nama kategori sudah digunakan.');
            }
        });

        if ($validator->fails()) {
            return redirect()->route('admin.forum-kategori')
                ->withErrors($validator)
                ->withInput()
                ->with(['_modal' => 'edit', '_kategori_id' => $kategori->id_forum_category]);
        }

        $kategori->update([
            'nama' => trim((string) $request->input('nama')),
            'deskripsi' => $request->filled('deskripsi') ? trim((string) $request->input('deskripsi')) : null,
            'icon' => $request->filled('icon') ? trim((string) $request->input('icon')) : 'tag',
            'warna' => $request->filled('warna') ? trim((string) $request->input('warna')) : '#3B82F6',
            'urutan' => (int) ($request->input('urutan') ?? $kategori->urutan),
            'is_active' => $this->resolveActive($request),
        ]);

        return redirect()->route('admin.forum-kategori')->with('success', 'Kategori forum berhasil diperbarui.');
    }

    public function toggleActive(int $id): RedirectResponse
    {
        $kategori = ForumCategory::findOrFail($id);
        $kategori->update(['is_active' => !$kategori->is_active]);

        return redirect()->route('admin.forum-kategori')->with(
            'success',
            $kategori->is_active ? 'Kategori diaktifkan.' : 'Kategori dinonaktifkan.'
        );
    }

    public function reorder(Request $request): RedirectResponse
    {
        $payload = $request->input('urutan', []);

        if (!is_array($payload)) {
            return redirect()->route('admin.forum-kategori')->with('error', 'Format urutan tidak valid.');
        }

        foreach ($payload as $row) {
            if (!isset($row['id'], $row['urutan'])) {
                continue;
            }
            ForumCategory::where('id_forum_category', (int) $row['id'])
                ->update(['urutan' => max(0, (int) $row['urutan'])]);
        }

        return redirect()->route('admin.forum-kategori')->with('success', 'Urutan kategori diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $kategori = ForumCategory::findOrFail($id);
        if ($kategori->topics()->exists()) {
            return redirect()->route('admin.forum-kategori')->with(
                'error',
                'Kategori tidak dapat dihapus karena masih memiliki topik. Pindahkan atau hapus topik terlebih dahulu.'
            );
        }

        $kategori->delete();

        return redirect()->route('admin.forum-kategori')->with('success', 'Kategori forum berhasil dihapus.');
    }

    private function resolveActive(Request $request): bool
    {
        $value = $request->input('is_active');
        if (is_bool($value)) {
            return $value;
        }
        if (is_string($value)) {
            return in_array(strtolower($value), ['1', 'true', 'on', 'aktif'], true);
        }

        return true;
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class AdminForumTopicController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $categoryId = $request->query('category');
        $statusFilter = (string) $request->query('status', 'all');

        $query = ForumTopic::query()
            ->with(['category:id_forum_category,nama,warna', 'author:id,name'])
            ->withCount('comments');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('isi', 'like', "%{$search}%");
            });
        }
        if (!empty($categoryId)) {
            $query->where('category_id', (int) $categoryId);
        }
        if (in_array($statusFilter, ['published', 'hidden', 'deleted'], true)) {
            $query->where('status', $statusFilter);
        }

        $topikPaginated = $query->orderByDesc('is_pinned')
            ->orderByDesc('last_activity_at')
            ->paginate(10)
            ->withQueryString();

        $totalAll = ForumTopic::count();
        $totalPublished = ForumTopic::where('status', 'published')->count();
        $totalHidden = ForumTopic::where('status', 'hidden')->count();
        $totalDeleted = ForumTopic::where('status', 'deleted')->count();
        $totalPinned = ForumTopic::where('is_pinned', true)->count();
        $totalLocked = ForumTopic::where('is_locked', true)->count();

        $categories = ForumCategory::query()
            ->ordered()
            ->get(['id_forum_category', 'nama', 'is_active']);

        return view('Auth.admin.forum-topik', [
            'topikPaginated' => $topikPaginated,
            'search' => $search,
            'categoryId' => $categoryId,
            'statusFilter' => $statusFilter,
            'categories' => $categories,
            'totalAll' => $totalAll,
            'totalPublished' => $totalPublished,
            'totalHidden' => $totalHidden,
            'totalDeleted' => $totalDeleted,
            'totalPinned' => $totalPinned,
            'totalLocked' => $totalLocked,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $topik = ForumTopic::with(['category:id_forum_category,nama,warna', 'author:id,name'])
            ->withCount('comments')
            ->findOrFail($id);

        return response()->json([
            'id_forum_topic' => $topik->id_forum_topic,
            'judul' => $topik->judul,
            'isi_preview' => \Illuminate\Support\Str::limit(strip_tags((string) $topik->isi), 220),
            'category' => $topik->category?->nama,
            'author_name' => $topik->author?->name ?? 'Anonim',
            'comments_count' => $topik->comments_count,
            'views' => $topik->views,
            'is_pinned' => (bool) $topik->is_pinned,
            'is_locked' => (bool) $topik->is_locked,
            'status' => $topik->status,
            'slug' => $topik->slug,
        ]);
    }

    public function togglePin(int $id): RedirectResponse
    {
        $topik = ForumTopic::findOrFail($id);
        $topik->update(['is_pinned' => !$topik->is_pinned]);

        return redirect()->route('admin.forum-topik')->with(
            'success',
            $topik->is_pinned ? 'Topik di-pin.' : 'Pin topik dilepas.'
        );
    }

    public function toggleLock(int $id): RedirectResponse
    {
        $topik = ForumTopic::findOrFail($id);
        $topik->update(['is_locked' => !$topik->is_locked]);

        return redirect()->route('admin.forum-topik')->with(
            'success',
            $topik->is_locked ? 'Topik dikunci. Mahasiswa tidak dapat membalas.' : 'Kunci topik dibuka.'
        );
    }

    public function setStatus(Request $request, int $id): RedirectResponse
    {
        $topik = ForumTopic::findOrFail($id);

        $data = $request->validate([
            'status' => ['required', Rule::in(['published', 'hidden', 'deleted'])],
        ]);

        $topik->update(['status' => $data['status']]);

        $label = match ($data['status']) {
            'published' => 'dipublikasikan',
            'hidden' => 'disembunyikan',
            'deleted' => 'ditandai dihapus',
        };

        return redirect()->route('admin.forum-topik')->with('success', "Topik berhasil {$label}.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $topik = ForumTopic::findOrFail($id);
        $topik->update(['status' => 'deleted']);
        // Cascade: also archive descendants so admin UI surfaces them as deleted.
        ForumComment::where('topic_id', $topik->id_forum_topic)
            ->where('status', '!=', 'deleted')
            ->update(['status' => 'deleted']);

        return redirect()->route('admin.forum-topik')->with('success', 'Topik ditandai dihapus. Komentar terkait disembunyikan dari publik.');
    }
}

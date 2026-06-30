<?php

namespace App\Http\Controllers\Auth;

use App\Models\ForumComment;
use App\Models\ForumTopic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class AdminForumCommentController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $topicId = $request->query('topic');
        $statusFilter = (string) $request->query('status', 'all');

        $query = ForumComment::query()
            ->with(['topic:id_forum_topic,judul,category_id', 'author:id,name', 'parent:id_forum_comment,isi']);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('isi', 'like', "%{$search}%")
                    ->orWhereHas('author', fn ($a) => $a->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('topic', fn ($t) => $t->where('judul', 'like', "%{$search}%"));
            });
        }
        if (!empty($topicId)) {
            $query->where('topic_id', (int) $topicId);
        }
        if (in_array($statusFilter, ['published', 'hidden', 'deleted'], true)) {
            $query->where('status', $statusFilter);
        }

        $komentarPaginated = $query->orderByDesc('id_forum_comment')
            ->paginate(12)
            ->withQueryString();

        $totalAll = ForumComment::count();
        $totalPublished = ForumComment::where('status', 'published')->count();
        $totalHidden = ForumComment::where('status', 'hidden')->count();
        $totalDeleted = ForumComment::where('status', 'deleted')->count();
        $totalReplies = ForumComment::whereNotNull('parent_id')->count();

        $recentTopics = ForumTopic::query()
            ->with('category:id_forum_category,nama')
            ->orderByDesc('last_activity_at')
            ->limit(40)
            ->get(['id_forum_topic', 'judul', 'category_id']);

        return view('Auth.admin.forum-komentar', [
            'komentarPaginated' => $komentarPaginated,
            'search' => $search,
            'topicId' => $topicId,
            'statusFilter' => $statusFilter,
            'totalAll' => $totalAll,
            'totalPublished' => $totalPublished,
            'totalHidden' => $totalHidden,
            'totalDeleted' => $totalDeleted,
            'totalReplies' => $totalReplies,
            'recentTopics' => $recentTopics,
        ]);
    }

    public function showTopic(int $topicId)
    {
        $topik = ForumTopic::with(['category:id_forum_category,nama,warna', 'author:id,name'])
            ->findOrFail($topicId);

        $komentars = ForumComment::with(['author:id,name', 'replies.author:id,name'])
            ->where('topic_id', $topicId)
            ->whereNull('parent_id')
            ->orderBy('id_forum_comment')
            ->paginate(15)
            ->withQueryString();

        return view('Auth.admin.forum-komentar-topic', [
            'topik' => $topik,
            'komentars' => $komentars,
        ]);
    }

    public function setStatus(Request $request, int $id): RedirectResponse
    {
        $komentar = ForumComment::findOrFail($id);

        $data = $request->validate([
            'status' => ['required', Rule::in(['published', 'hidden', 'deleted'])],
            'return_to' => ['nullable', 'string'],
        ]);

        $komentar->update(['status' => $data['status']]);

        $label = match ($data['status']) {
            'published' => 'dipublikasikan',
            'hidden' => 'disembunyikan',
            'deleted' => 'ditandai dihapus',
        };

        $redirect = $data['return_to'] ?? 'admin.forum-komentar';

        return redirect()->route($redirect)->with('success', "Komentar berhasil {$label}.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $komentar = ForumComment::findOrFail($id);
        $topicId = $komentar->topic_id;
        $komentar->update(['status' => 'deleted']);
        // Cascade: collapse nested reply chain so descendants are not orphaned live.
        ForumComment::where('parent_id', $komentar->id_forum_comment)
            ->where('status', '!=', 'deleted')
            ->update(['status' => 'deleted']);

        if ($topicId) {
            $latest = ForumComment::where('topic_id', $topicId)
                ->where('status', 'published')
                ->latest('id_forum_comment')
                ->first();
            ForumTopic::where('id_forum_topic', $topicId)->update([
                'last_activity_at' => optional($latest)->created_at ?? now(),
            ]);
        }

        return redirect()->back()->with('success', 'Komentar ditandai dihapus.');
    }
}

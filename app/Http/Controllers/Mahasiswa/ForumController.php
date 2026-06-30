<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Models\ForumCategory;
use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $categorySlug = $request->query('category');
        $sort = (string) $request->query('sort', 'terbaru');
        $onlyPinned = $request->boolean('pinned');

        $categories = ForumCategory::query()
            ->active()
            ->ordered()
            ->withCount(['topics' => fn ($q) => $q->where('status', 'published')])
            ->get();

        $query = ForumTopic::query()
            ->with(['category:id_forum_category,nama,warna,slug', 'author:id,name'])
            ->withCount(['publishedComments']);
        $query->whereIn('status', ['published']);
        if ($onlyPinned) {
            $query->where('is_pinned', true);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('isi', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($c) use ($search) {
                        $c->where('nama', 'like', "%{$search}%");
                    });
            });
        }
        if (!empty($categorySlug)) {
            $query->whereHas('category', fn ($c) => $c->where('slug', $categorySlug));
        }

        switch ($sort) {
            case 'terpopuler':
                $query->orderByDesc('views');
                break;
            case 'belum_dijawab':
                $query->orderBy('last_activity_at')->whereDoesntHave('publishedComments');
                break;
            case 'pinned':
                $query->orderByDesc('is_pinned')->orderByDesc('last_activity_at');
                break;
            case 'terbaru':
            default:
                $query->orderByDesc('is_pinned')->orderByDesc('last_activity_at');
                break;
        }

        $topikPaginated = $query->paginate(10)->withQueryString();

        $stats = [
            'topics' => ForumTopic::where('status', 'published')->count(),
            'comments' => ForumComment::where('status', 'published')->count(),
            'categories' => $categories->count(),
            'pinned' => ForumTopic::where('status', 'published')->where('is_pinned', true)->count(),
        ];

        return view('pages.mahasiswa.forum', [
            'kategoriList' => $categories,
            'topikPaginated' => $topikPaginated,
            'search' => $search,
            'sort' => $sort,
            'onlyPinned' => $onlyPinned,
            'categorySlug' => $categorySlug,
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        $kategoriList = ForumCategory::query()->active()->ordered()->get();

        return view('pages.mahasiswa.forum-create', [
            'kategoriList' => $kategoriList,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::guard('mahasiswa')->user();
        if (!$user) {
            return redirect()->route('mahasiswa.login');
        }

        $data = $request->validate([
            'category_id' => ['required', Rule::exists((new ForumCategory())->getTable(), 'id_forum_category')->where(fn ($q) => $q->where('is_active', true))],
            'judul' => ['required', 'string', 'max:200'],
            'isi' => ['required', 'string', 'min:5', 'max:8000'],
        ]);

        $topik = ForumTopic::create([
            'category_id' => (int) $data['category_id'],
            'user_id' => $user->id,
            'judul' => trim((string) $data['judul']),
            'isi' => trim((string) $data['isi']),
            'status' => 'published',
            'last_activity_at' => now(),
        ]);

        return redirect()->route('mahasiswa.forum.show', ['slug' => $topik->slug])->with('success', 'Topik berhasil dibuat.');
    }

    public function show(Request $request, string $slug)
    {
        $topik = ForumTopic::with(['category:id_forum_category,nama,warna,slug', 'author:id,name'])
            ->whereIn('status', ['published', 'hidden'])
            ->where('slug', $slug)
            ->firstOrFail();

        $isLocked = (bool) $topik->is_locked;

        $komentars = ForumComment::with([
            'author:id,name',
            'publishedReplies.author:id,name',
        ])
            ->where('topic_id', $topik->id_forum_topic)
            ->whereNull('parent_id')
            ->where('status', 'published')
            ->orderBy('id_forum_comment')
            ->get();

        $related = ForumTopic::query()
            ->with('category:id_forum_category,nama,warna')
            ->where('category_id', $topik->category_id)
            ->where('id_forum_topic', '!=', $topik->id_forum_topic)
            ->where('status', 'published')
            ->orderByDesc('last_activity_at')
            ->limit(5)
            ->get(['id_forum_topic', 'judul', 'slug', 'category_id', 'last_activity_at', 'is_pinned']);

        $sessionKey = 'forum_topic_view_' . $topik->id_forum_topic;
        if (!$request->session()->has($sessionKey)) {
            $topik->incrementViews();
            $request->session()->put($sessionKey, true);
        }

        return view('pages.mahasiswa.forum-detail', [
            'topik' => $topik,
            'komentars' => $komentars,
            'related' => $related,
            'isLocked' => $isLocked,
        ]);
    }

    public function storeComment(Request $request, string $slug): RedirectResponse
    {
        $user = Auth::guard('mahasiswa')->user();
        if (!$user) {
            return redirect()->route('mahasiswa.login');
        }

        $topik = ForumTopic::whereIn('status', ['published'])->where('slug', $slug)->firstOrFail();

        if ($topik->is_locked) {
            return redirect()->route('mahasiswa.forum.show', ['slug' => $slug])
                ->with('error', 'Diskusi telah dikunci oleh administrator.');
        }

        $data = $request->validate([
            'parent_id' => ['nullable', Rule::exists((new ForumComment())->getTable(), 'id_forum_comment')->where(fn ($q) => $q->where('topic_id', $topik->id_forum_topic)->whereNull('parent_id'))],
            'isi' => ['required', 'string', 'min:2', 'max:4000'],
        ]);

        $komentar = ForumComment::create([
            'topic_id' => $topik->id_forum_topic,
            'user_id' => $user->id,
            'parent_id' => !empty($data['parent_id']) ? (int) $data['parent_id'] : null,
            'isi' => trim((string) $data['isi']),
            'status' => 'published',
        ]);

        $topik->touchActivity();

        $this->notifyRecipients($komentar, $topik, $user->name);

        return redirect()->route('mahasiswa.forum.show', ['slug' => $slug])
            ->with('success', 'Komentar terkirim.');
    }

    private function notifyRecipients(ForumComment $komentar, ForumTopic $topik, string $authorName): void
    {
        if ($komentar->parent_id) {
            $parent = ForumComment::find($komentar->parent_id);
            if ($parent && $parent->user_id && $parent->user_id !== $komentar->user_id) {
                Notification::notifyMahasiswa(
                    (int) $parent->user_id,
                    'Balasan Komentar',
                    "{$authorName} membalas komentar Anda di topik \"" . \Illuminate\Support\Str::limit($topik->judul, 60) . "\".",
                    'umum',
                    'chat-bubble-left',
                    '#8B5CF6'
                );
            }
        }

        if ($topik->user_id && $topik->user_id !== $komentar->user_id) {
            Notification::notifyMahasiswa(
                (int) $topik->user_id,
                'Komentar Baru di Topik Anda',
                "{$authorName} mengomentari topik \"" . \Illuminate\Support\Str::limit($topik->judul, 60) . "\".",
                'umum',
                'chat-bubble-left',
                '#3B82F6'
            );
        }
    }
}

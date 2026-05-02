<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\SupportFaq;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    public function ask(Request $request)
    {
        $dosen = Auth::guard('dosen')->user();

        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi dosen tidak valid.',
            ], 401);
        }

        $validated = $request->validate([
            'subject' => 'nullable|string|max:255',
            'question' => 'required|string|min:5|max:2000',
        ]);

        $question = trim($validated['question']);
        $faq = $this->findBestFaqMatch($question);

        if ($faq !== null) {
            return response()->json([
                'success' => true,
                'resolved' => true,
                'message' => 'Pertanyaan Anda cocok dengan jawaban FAQ.',
                'data' => [
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                ],
            ]);
        }

        $ticket = SupportTicket::create([
            'id_dosen' => $dosen->id,
            'subject' => trim((string) ($validated['subject'] ?? 'Pertanyaan Support Dosen')),
            'question' => $question,
            'status' => 'open',
        ]);

        AdminNotification::notifyAllAdmins(
            'Support Ticket Dosen Baru',
            "{$dosen->name} mengirim pertanyaan support dosen: {$ticket->subject}",
            'info',
            'support',
            route('admin.support-tickets', ['ticket' => $ticket->id_support_ticket]) . '#ticket-' . $ticket->id_support_ticket
        );

        return response()->json([
            'success' => true,
            'resolved' => false,
            'message' => 'Pertanyaan belum terjawab di FAQ dan sudah diteruskan ke admin.',
            'data' => [
                'ticket_id' => $ticket->id_support_ticket,
                'status' => $ticket->status,
            ],
        ]);
    }

    private function findBestFaqMatch(string $question): ?SupportFaq
    {
        $keywords = $this->extractKeywords($question);
        if ($keywords->isEmpty()) {
            return null;
        }

        $bestFaq = null;
        $bestScore = 0;

        foreach (SupportFaq::query()->where('is_active', true)->get() as $faq) {
            $haystack = Str::lower($faq->question . ' ' . $faq->answer);
            $score = $keywords->reduce(function (int $carry, string $keyword) use ($haystack) {
                return $carry + (Str::contains($haystack, $keyword) ? 1 : 0);
            }, 0);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestFaq = $faq;
            }
        }

        return $bestScore >= 2 ? $bestFaq : null;
    }

    private function extractKeywords(string $text): Collection
    {
        return collect(preg_split('/[^a-zA-Z0-9]+/u', Str::lower($text)) ?: [])
            ->filter(fn (string $part) => mb_strlen($part) >= 4)
            ->unique()
            ->values();
    }
}

<x-layouts.admin title="Manajemen Chat" active="chat">
    <div
        x-data="adminChatManager()"
        x-init="init()"
        class="h-[calc(100vh-96px)] flex flex-col gap-4"
    >
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Chat</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Monitor percakapan mahasiswa-dosen, pantau aktivitas, dan moderasi pesan.</p>
        </div>

        <div class="responsive-grid-stats">
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Percakapan</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.total"></p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Aktif 24 Jam</p>
                <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400" x-text="stats.active24h"></p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Sedang Berlangsung</p>
                <p class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="stats.ongoing"></p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Pesan Hari Ini</p>
                <p class="mt-1 text-2xl font-bold text-violet-600 dark:text-violet-400" x-text="stats.todayMessages"></p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-3 sm:p-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div class="sm:col-span-2 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        x-model="search"
                        @input.debounce.250ms="applyFilters()"
                        placeholder="Cari nama mahasiswa, dosen, atau isi chat..."
                        class="w-full pl-9 pr-3 py-2.5 text-sm rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
                <div>
                    <select
                        x-model="statusFilter"
                        @change="applyFilters()"
                        class="w-full px-3 py-2.5 text-sm rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="all">Semua Status</option>
                        <option value="ongoing">Sedang Chat</option>
                        <option value="active24h">Aktif 24 Jam</option>
                        <option value="idle">Idle</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex-1 min-h-0 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 rounded-2xl overflow-hidden flex">
            <div
                class="w-full lg:w-[360px] border-r border-gray-100 dark:border-gray-700 flex flex-col"
                :class="{ 'hidden lg:flex': activeConversationId, 'flex': !activeConversationId }"
            >
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Percakapan Terdaftar</h2>
                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="`${filteredConversations.length} chat`"></span>
                </div>

                <div x-show="isLoadingConversations" class="flex-1 flex items-center justify-center">
                    <svg class="animate-spin h-7 w-7 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>

                <div x-show="!isLoadingConversations && filteredConversations.length === 0" class="flex-1 flex items-center justify-center px-6 text-sm text-gray-500 dark:text-gray-400 text-center">
                    Tidak ada percakapan yang cocok dengan filter.
                </div>

                <div x-show="!isLoadingConversations && filteredConversations.length > 0" class="flex-1 overflow-y-auto">
                    <template x-for="conv in filteredConversations" :key="conv.id">
                        <button
                            type="button"
                            @click="selectConversation(conv.id)"
                            class="w-full text-left px-4 py-3 border-l-4 transition hover:bg-gray-50 dark:hover:bg-gray-700/40"
                            :class="activeConversationId === conv.id ? 'bg-blue-50 dark:bg-blue-500/10 border-blue-500' : 'border-transparent'"
                        >
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-semibold text-gray-600 dark:text-gray-300 flex-shrink-0" x-text="initials(conv.student_name)"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-semibold text-sm text-gray-900 dark:text-white truncate" x-text="conv.student_name"></p>
                                        <span class="text-[11px] text-gray-400" x-text="formatTime(conv.last_message_at)"></span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5" x-text="`Dosen: ${conv.lecturer_name}`"></p>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 truncate mt-0.5" x-text="conv.last_message || 'Belum ada pesan'"></p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium"
                                              :class="badgeClass(conv.status)"
                                              x-text="badgeText(conv.status)"></span>
                                        <span class="text-[11px] text-gray-400" x-text="`${conv.message_count} pesan`"></span>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <div class="flex-1 min-w-0 flex flex-col">
                <template x-if="!activeConversation">
                    <div class="flex-1 flex flex-col items-center justify-center text-center px-6">
                        <div class="w-20 h-20 rounded-full bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pilih Percakapan</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Klik salah satu chat untuk melihat isi percakapan dan moderasi pesan.</p>
                    </div>
                </template>

                <template x-if="activeConversation">
                    <div class="flex-1 min-h-0 flex flex-col">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <button type="button" @click="activeConversationId = null" class="lg:hidden p-2 -ml-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white truncate" x-text="`${activeConversation.student_name} • ${activeConversation.lecturer_name}`"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate" x-text="`Terakhir aktif: ${formatDateTime(activeConversation.last_message_at)}`"></p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold"
                                  :class="badgeClass(activeConversation.status)"
                                  x-text="badgeText(activeConversation.status)"></span>
                        </div>

                        <div id="admin-chat-messages" class="flex-1 overflow-y-auto px-4 py-4 bg-gray-50/60 dark:bg-gray-900/20 space-y-3">
                            <div x-show="isLoadingMessages" class="py-6 flex justify-center">
                                <svg class="animate-spin h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </div>

                            <div x-show="!isLoadingMessages && messages.length === 0" class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada pesan pada percakapan ini.
                            </div>

                            <template x-for="message in messages" :key="message.id">
                                <div class="max-w-[92%] sm:max-w-[82%] group"
                                     :class="message.sender_role === 'admin' ? 'ml-auto' : ''">
                                    <div class="rounded-2xl px-3.5 py-2.5 shadow-sm border text-sm"
                                         :class="bubbleClass(message.sender_role)">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <p class="font-semibold text-xs mb-1 opacity-80" x-text="message.sender_name"></p>
                                                <p class="whitespace-pre-wrap break-words" x-text="message.content"></p>
                                                <p class="text-[11px] mt-1 opacity-70" x-text="formatDateTime(message.created_at)"></p>
                                            </div>
                                            <button
                                                type="button"
                                                @click="deleteMessage(message)"
                                                class="opacity-0 group-hover:opacity-100 transition text-red-500 hover:text-red-600 p-1 rounded"
                                                title="Hapus pesan"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                            <form @submit.prevent="sendAdminMessage" class="space-y-2">
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <div class="flex-1">
                                        <textarea
                                            x-model="adminMessage"
                                            @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); sendAdminMessage(); }"
                                            rows="1"
                                            placeholder="Balas langsung di riwayat percakapan ini..."
                                            class="w-full px-3 py-2.5 text-sm rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                        ></textarea>
                                    </div>
                                    <button
                                        type="submit"
                                        :disabled="isSendingMessage || !adminMessage.trim()"
                                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                                    >
                                        <svg x-show="!isSendingMessage" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <svg x-show="isSendingMessage" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Kirim
                                    </button>
                                </div>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                    Pesan admin akan masuk ke riwayat percakapan yang sedang dipilih.
                                </p>
                            </form>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminChatManager', () => ({
                conversations: [],
                filteredConversations: [],
                activeConversationId: null,
                messages: [],
                adminMessage: '',
                search: '',
                statusFilter: 'all',
                isLoadingConversations: false,
                isLoadingMessages: false,
                isSendingMessage: false,
                poller: null,
                stats: {
                    total: 0,
                    active24h: 0,
                    ongoing: 0,
                    todayMessages: 0,
                },

                get activeConversation() {
                    return this.conversations.find((item) => item.id === this.activeConversationId) || null;
                },

                init() {
                    this.fetchConversations();
                    this.poller = setInterval(() => {
                        this.fetchConversations(false);
                        if (this.activeConversationId) {
                            this.fetchMessages(this.activeConversationId, true);
                        }
                    }, 5000);

                    window.addEventListener('beforeunload', () => {
                        if (this.poller) clearInterval(this.poller);
                    });
                },

                async fetchConversations(showLoader = true) {
                    if (showLoader) this.isLoadingConversations = true;
                    try {
                        const response = await fetch('/admin/messages/conversations', {
                            credentials: 'same-origin',
                            headers: { Accept: 'application/json' },
                        });

                        if (!response.ok) throw new Error('Endpoint not ready');
                        const payload = await response.json();
                        const serverData = Array.isArray(payload?.data) ? payload.data : [];
                        this.conversations = this.normalizeConversations(serverData);
                    } catch (error) {
                        this.conversations = this.mockConversations();
                    } finally {
                        this.recalculateStats();
                        this.applyFilters();
                        if (!this.activeConversationId && this.filteredConversations.length > 0) {
                            this.selectConversation(this.filteredConversations[0].id);
                        }
                        if (showLoader) this.isLoadingConversations = false;
                    }
                },

                normalizeConversations(items) {
                    return items.map((item, idx) => ({
                        id: item.id || `conv-${idx + 1}`,
                        student_name: item.student_name || 'Mahasiswa',
                        lecturer_name: item.lecturer_name || 'Dosen',
                        status: item.status || 'idle',
                        last_message: item.last_message || '',
                        last_message_at: item.last_message_at || new Date().toISOString(),
                        message_count: Number(item.message_count || 0),
                    }));
                },

                mockConversations() {
                    return [
                        { id: 'c-1', student_name: 'Rafi Akbar', lecturer_name: 'Dosen Matematika', status: 'ongoing', last_message: 'Pak, untuk quiz deadline jam berapa?', last_message_at: new Date(Date.now() - 2 * 60 * 1000).toISOString(), message_count: 34 },
                        { id: 'c-2', student_name: 'Nabila Putri', lecturer_name: 'Dosen Bahasa Inggris', status: 'active24h', last_message: 'Baik bu, saya revisi tugasnya.', last_message_at: new Date(Date.now() - 90 * 60 * 1000).toISOString(), message_count: 18 },
                        { id: 'c-3', student_name: 'Gilang Pratama', lecturer_name: 'Dosen Pemrograman', status: 'idle', last_message: 'Terima kasih penjelasannya pak.', last_message_at: new Date(Date.now() - 30 * 60 * 60 * 1000).toISOString(), message_count: 9 },
                        { id: 'c-4', student_name: 'Salsa Maharani', lecturer_name: 'Dosen UI/UX', status: 'ongoing', last_message: 'Saya sudah upload revisi wireframe.', last_message_at: new Date(Date.now() - 4 * 60 * 1000).toISOString(), message_count: 26 },
                    ];
                },

                applyFilters() {
                    const query = this.search.trim().toLowerCase();
                    this.filteredConversations = this.conversations.filter((conv) => {
                        if (this.statusFilter !== 'all' && conv.status !== this.statusFilter) return false;
                        if (!query) return true;
                        return [conv.student_name, conv.lecturer_name, conv.last_message]
                            .join(' ')
                            .toLowerCase()
                            .includes(query);
                    });

                    if (this.activeConversationId && !this.filteredConversations.some((c) => c.id === this.activeConversationId)) {
                        this.activeConversationId = null;
                        this.messages = [];
                    }
                },

                recalculateStats() {
                    const now = Date.now();
                    this.stats.total = this.conversations.length;
                    this.stats.active24h = this.conversations.filter((c) => now - new Date(c.last_message_at).getTime() <= 24 * 60 * 60 * 1000).length;
                    this.stats.ongoing = this.conversations.filter((c) => c.status === 'ongoing').length;
                    this.stats.todayMessages = this.conversations.reduce((sum, c) => sum + c.message_count, 0);
                },

                async selectConversation(conversationId) {
                    this.activeConversationId = conversationId;
                    await this.fetchMessages(conversationId);
                },

                async fetchMessages(conversationId, silent = false) {
                    if (!silent) this.isLoadingMessages = true;
                    try {
                        const response = await fetch(`/admin/messages/chat/${conversationId}`, {
                            credentials: 'same-origin',
                            headers: { Accept: 'application/json' },
                        });
                        if (!response.ok) throw new Error('Endpoint not ready');
                        const payload = await response.json();
                        const serverData = Array.isArray(payload?.data) ? payload.data : [];
                        this.messages = this.normalizeMessages(serverData);
                    } catch (error) {
                        this.messages = this.mockMessages(conversationId);
                    } finally {
                        if (!silent) this.isLoadingMessages = false;
                        this.$nextTick(() => this.scrollToBottom());
                    }
                },

                normalizeMessages(items) {
                    return items.map((item, idx) => ({
                        id: item.id || `msg-${idx + 1}`,
                        sender_name: item.sender_name || 'User',
                        sender_role: item.sender_role || this.mapSenderTypeToRole(item.sender_type),
                        content: item.content || '',
                        created_at: item.created_at || new Date().toISOString(),
                    }));
                },

                mockMessages(conversationId) {
                    const common = [
                        { id: `${conversationId}-1`, sender_name: 'Mahasiswa', sender_role: 'student', content: 'Pak, saya mau konfirmasi tugas minggu ini.', created_at: new Date(Date.now() - 50 * 60 * 1000).toISOString() },
                        { id: `${conversationId}-2`, sender_name: 'Dosen', sender_role: 'lecturer', content: 'Silakan submit sebelum jam 23:59 malam ini.', created_at: new Date(Date.now() - 46 * 60 * 1000).toISOString() },
                        { id: `${conversationId}-3`, sender_name: 'Mahasiswa', sender_role: 'student', content: 'Baik pak, terima kasih.', created_at: new Date(Date.now() - 42 * 60 * 1000).toISOString() },
                    ];
                    return common;
                },

                async deleteMessage(message) {
                    const confirmed = await this.confirmDelete(message);
                    if (!confirmed) return;

                    const previous = [...this.messages];
                    this.messages = this.messages.filter((m) => m.id !== message.id);

                    try {
                        const response = await fetch(`/admin/messages/${message.id}`, {
                            method: 'DELETE',
                            credentials: 'same-origin',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                Accept: 'application/json',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Delete endpoint not ready');
                        }
                        this.showToast('Pesan berhasil dihapus.', 'success');
                    } catch (error) {
                        this.messages = previous;
                        this.showToast('Backend hapus chat belum aktif. UI hanya simulasi.', 'info');
                    }
                },

                async sendAdminMessage() {
                    if (!this.activeConversation || !this.adminMessage.trim() || this.isSendingMessage) return;
                    this.isSendingMessage = true;

                    const content = this.adminMessage.trim();
                    const optimisticMessage = {
                        id: `tmp-admin-${Date.now()}`,
                        sender_name: 'Admin',
                        sender_role: 'admin',
                        content,
                        created_at: new Date().toISOString(),
                    };

                    this.messages.push(optimisticMessage);
                    this.adminMessage = '';
                    this.$nextTick(() => this.scrollToBottom());

                    try {
                        const response = await fetch('/admin/messages/send', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                Accept: 'application/json',
                            },
                            body: JSON.stringify({
                                conversation_id: this.activeConversationId,
                                content,
                            }),
                        });

                        if (!response.ok) {
                            throw new Error('Send endpoint not ready');
                        }

                        const payload = await response.json();
                        const sentMessage = payload?.data ? this.normalizeMessages([payload.data])[0] : null;
                        if (sentMessage) {
                            this.messages = this.messages.filter((m) => m.id !== optimisticMessage.id);
                            this.messages.push(sentMessage);
                        }
                        this.showToast('Pesan admin terkirim.', 'success');
                    } catch (error) {
                        // Keep optimistic message for frontend-first flow.
                        this.showToast('Backend kirim chat belum aktif. Pesan tampil sebagai simulasi frontend.', 'info');
                    } finally {
                        this.touchActiveConversation(content);
                        this.recalculateStats();
                        this.isSendingMessage = false;
                        this.$nextTick(() => this.scrollToBottom());
                    }
                },

                touchActiveConversation(lastMessage) {
                    const idx = this.conversations.findIndex((c) => c.id === this.activeConversationId);
                    if (idx === -1) return;
                    const updated = {
                        ...this.conversations[idx],
                        last_message: lastMessage,
                        last_message_at: new Date().toISOString(),
                        status: 'ongoing',
                        message_count: Number(this.conversations[idx].message_count || 0) + 1,
                    };
                    this.conversations.splice(idx, 1, updated);
                    this.applyFilters();
                },

                mapSenderTypeToRole(senderType) {
                    if (senderType === 'admin') return 'admin';
                    if (senderType === 'dosen' || senderType === 'lecturer') return 'lecturer';
                    return 'student';
                },

                confirmDelete(message) {
                    if (window.Swal && typeof window.Swal.fire === 'function') {
                        return window.Swal.fire({
                            title: 'Hapus Pesan?',
                            text: `Pesan dari ${message.sender_name} akan dihapus.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true,
                            focusCancel: true,
                            buttonsStyling: false,
                            customClass: {
                                container: 'font-inter',
                                actions: 'flex gap-2',
                                confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-5 rounded-lg transition-colors',
                                cancelButton: 'bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-5 rounded-lg transition-colors border border-gray-300'
                            }
                        }).then((r) => r.isConfirmed);
                    }
                    return Promise.resolve(window.confirm('Hapus pesan ini?'));
                },

                showToast(message, icon = 'info') {
                    if (window.Swal && typeof window.Swal.fire === 'function') {
                        window.Swal.fire({
                            toast: true,
                            position: 'top-end',
                            timer: 1800,
                            showConfirmButton: false,
                            icon,
                            title: message,
                        });
                        return;
                    }
                    console.log(message);
                },

                badgeText(status) {
                    if (status === 'ongoing') return 'Sedang Chat';
                    if (status === 'active24h') return 'Aktif 24 Jam';
                    return 'Idle';
                },

                badgeClass(status) {
                    if (status === 'ongoing') return 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300';
                    if (status === 'active24h') return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300';
                    return 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                },

                bubbleClass(role) {
                    if (role === 'admin') return 'bg-red-50 text-red-900 border-red-200 dark:bg-red-500/10 dark:text-red-200 dark:border-red-500/30';
                    if (role === 'lecturer' || role === 'dosen') return 'bg-blue-50 text-blue-900 border-blue-200 dark:bg-blue-500/10 dark:text-blue-200 dark:border-blue-500/30';
                    return 'bg-white text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700';
                },

                initials(name) {
                    return (name || 'U')
                        .split(' ')
                        .filter(Boolean)
                        .slice(0, 2)
                        .map((part) => part[0]?.toUpperCase() || '')
                        .join('');
                },

                formatTime(iso) {
                    if (!iso) return '-';
                    const date = new Date(iso);
                    return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                },

                formatDateTime(iso) {
                    if (!iso) return '-';
                    const date = new Date(iso);
                    return date.toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        hour: '2-digit',
                        minute: '2-digit',
                    });
                },

                scrollToBottom() {
                    const box = document.getElementById('admin-chat-messages');
                    if (box) box.scrollTop = box.scrollHeight;
                },
            }));
        });
    </script>
    @endpush
</x-layouts.admin>

<x-layouts.dosen title="Pesan" active="pesan">
    <div x-data="dosenChatSystem()" x-init="initChat()" class="h-[calc(100vh-120px)] flex flex-col">
        <div class="mb-4 hidden flex-shrink-0 md:block">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pesan</h1>
            <p class="mt-1 text-gray-500 dark:text-gray-400">Komunikasi realtime dengan mahasiswa yang mengikuti kursus Anda.</p>
        </div>
        <div x-show="bootcampContext" x-cloak class="mb-4 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-200">
            <p class="font-semibold">Konteks dari Bootcamp</p>
            <p class="mt-1" x-text="bootcampContext?.label"></p>
        </div>

        <div class="flex flex-1 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-700/50 dark:bg-gray-800">
            <div class="flex w-full flex-col border-r border-gray-100 dark:border-gray-700 md:w-80"
                 :class="{'hidden md:flex': activeConversation, 'flex': !activeConversation}">
                <div class="border-b border-gray-100 p-3 dark:border-gray-700">
                    <div class="relative">
                        <input
                            x-model="searchQuery"
                            @input.debounce.400ms="fetchConversations()"
                            type="text"
                            placeholder="Cari mahasiswa..."
                            class="w-full rounded-lg border-0 bg-gray-50 py-2 pl-9 pr-4 text-sm text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300"
                        >
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <div x-show="isLoadingConversations" class="flex flex-1 items-center justify-center">
                    <svg class="h-8 w-8 animate-spin text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <div x-show="!isLoadingConversations && conversations.length === 0" class="flex flex-1 flex-col items-center justify-center p-4 text-center">
                    <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada mahasiswa yang bisa dihubungi.</p>
                </div>

                <div x-show="!isLoadingConversations && conversations.length > 0" class="flex-1 overflow-y-auto">
                    <template x-for="conv in conversations" :key="conv.student_id">
                        <button type="button"
                                @click="selectConversation(conv)"
                                class="block w-full cursor-pointer border-l-4 px-3 py-3 text-left transition"
                                :class="activeConversation && activeConversation.student_id === conv.student_id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-transparent hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                            <div class="flex items-center gap-3">
                                <div class="relative flex-shrink-0">
                                    <img :src="conv.student_avatar" :alt="conv.student_name" class="h-10 w-10 rounded-full bg-gray-200 object-cover">
                                    <span x-show="conv.unread_count > 0" class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full border-2 border-white bg-blue-500 text-xs font-bold text-white dark:border-gray-800" x-text="conv.unread_count"></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="mb-1 flex items-center justify-between gap-2">
                                        <p class="truncate text-sm font-semibold text-gray-900 dark:text-white" x-text="conv.student_name"></p>
                                        <span class="text-xs text-gray-400" x-text="formatTime(conv.last_message_time)"></span>
                                    </div>
                                    <p class="truncate text-xs text-gray-500 dark:text-gray-400" x-text="conv.last_message || 'Mulai percakapan'"></p>
                                    <p class="mt-1 truncate text-[11px] text-gray-400" x-text="conv.student_nomor_induk || '-'"></p>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <div class="flex flex-1 flex-col bg-gray-50/30 dark:bg-gray-900/10"
                 :class="{'flex': activeConversation, 'hidden md:flex': !activeConversation}">
                <div x-show="!activeConversation" class="flex flex-1 flex-col items-center justify-center p-8 text-center">
                    <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                        <svg class="h-12 w-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Pilih Percakapan</h3>
                    <p class="max-w-sm text-gray-500 dark:text-gray-400">Cari mahasiswa dari daftar kiri untuk mulai chat realtime.</p>
                </div>

                <div x-show="activeConversation" class="flex h-full flex-1 flex-col overflow-hidden">
                    <div class="flex flex-shrink-0 items-center justify-between border-b border-gray-100 bg-white px-5 py-3 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center gap-3">
                            <button @click="activeConversation = null; messages = []; showStudentProfile = false" class="-ml-2 rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>

                            <img :src="activeConversation?.student_avatar" :alt="activeConversation?.student_name" class="h-10 w-10 rounded-full bg-gray-200 object-cover">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-gray-900 dark:text-white" x-text="activeConversation?.student_name"></p>
                                <p class="truncate text-xs text-gray-500 dark:text-gray-400" x-text="activeConversation?.student_nomor_induk"></p>
                                <p class="mt-0.5 text-[10px] font-medium text-emerald-500 dark:text-emerald-400">Chat realtime aktif</p>
                            </div>
                        </div>

                        <button @click="showStudentProfile = !showStudentProfile" class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700" title="Lihat profil mahasiswa">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                    </div>

                    <div id="messages-container" class="flex-1 space-y-4 overflow-y-auto bg-gray-50/50 p-5 scroll-smooth dark:bg-gray-900/30">
                        <div x-show="isLoadingMessages" class="flex justify-center py-4">
                            <svg class="h-6 w-6 animate-spin text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>

                        <div x-show="!isLoadingMessages && messages.length === 0" class="py-10 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada pesan. Silakan mulai percakapan dengan mahasiswa ini.</p>
                        </div>

                        <template x-for="msg in messages" :key="msg.id">
                            <div class="flex max-w-[88%] items-end gap-2 sm:max-w-[82%] md:max-w-[75%]"
                                 :class="msg.sender_type === 'dosen' ? 'ml-auto flex-row-reverse' : ''">
                                <template x-if="msg.sender_type === 'admin'">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 text-[11px] font-semibold text-red-700">
                                        AD
                                    </div>
                                </template>
                                <template x-if="msg.sender_type !== 'dosen' && msg.sender_type !== 'admin'">
                                    <img :src="activeConversation?.student_avatar" :alt="activeConversation?.student_name" class="h-8 w-8 rounded-full object-cover">
                                </template>
                                <div>
                                    <span x-show="msg.sender_type === 'admin'" class="mb-1 block text-[11px] font-semibold text-red-600">Admin DigiKampus</span>
                                    <div class="rounded-2xl px-4 py-2.5 text-sm shadow-sm"
                                         :class="msg.sender_type === 'dosen'
                                             ? 'rounded-br-sm bg-blue-500 text-white'
                                             : (msg.sender_type === 'admin'
                                                 ? 'rounded-bl-sm border border-red-200 bg-red-50 text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-200'
                                                 : 'rounded-bl-sm bg-white text-gray-700 dark:bg-gray-800 dark:text-gray-300')">
                                        <p class="whitespace-pre-wrap break-words" x-text="msg.content"></p>
                                    </div>
                                    <span class="mt-1 block text-[10px] text-gray-400"
                                          :class="msg.sender_type === 'dosen' ? 'text-right' : 'text-left'"
                                          x-text="formatTime(msg.created_at)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex-shrink-0 border-t border-gray-100 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-800">
                        <form @submit.prevent="sendMessage" class="flex items-end gap-3">
                            <div class="relative flex-1">
                                <textarea
                                    x-model="newMessage"
                                    @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); sendMessage(); }"
                                    placeholder="Ketik pesan..."
                                    rows="1"
                                    class="max-h-32 w-full resize-none rounded-xl border-0 bg-gray-50 px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300"
                                    style="min-height: 44px;"
                                ></textarea>
                            </div>
                            <button type="submit"
                                    :disabled="!newMessage.trim() || isSending"
                                    class="mb-0.5 rounded-xl bg-blue-500 p-3 text-white shadow-sm transition hover:bg-blue-600 disabled:cursor-not-allowed disabled:opacity-50">
                                <svg x-show="!isSending" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <svg x-show="isSending" class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div x-show="activeConversation && showStudentProfile"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 class="absolute right-0 z-10 h-full w-80 overflow-y-auto border-l border-gray-100 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800 md:relative md:shadow-none"
                 style="display: none;">
                <div class="p-4 md:hidden">
                    <div class="flex justify-end">
                        <button @click="showStudentProfile = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="border-b border-gray-100 p-5 text-center dark:border-gray-700">
                    <img :src="activeConversation?.student_avatar" :alt="activeConversation?.student_name" class="mx-auto mb-3 h-20 w-20 rounded-full bg-gray-200 object-cover">
                    <h3 class="font-bold text-gray-900 dark:text-white" x-text="activeConversation?.student_name"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'Nomor Induk: ' + (activeConversation?.student_nomor_induk || '-')"></p>
                </div>

                <div class="space-y-4 p-5">
                    <div>
                        <p class="mb-1 text-xs font-medium uppercase text-gray-400">Email</p>
                        <p class="truncate text-sm text-blue-500" x-text="activeConversation?.student_email || '-'"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dosenChatSystem', () => ({
                conversations: [],
                messages: [],
                activeConversation: null,
                searchQuery: '',
                newMessage: '',
                isLoadingConversations: false,
                isLoadingMessages: false,
                isSending: false,
                showStudentProfile: false,
                pollingInterval: null,
                bootcampContext: null,

                async initChat() {
                    this.readBootcampContext();
                    await this.fetchConversations();

                    if (this.bootcampContext && !this.activeConversation && this.conversations.length > 0) {
                        await this.selectConversation(this.conversations[0]);
                    }

                    this.pollingInterval = setInterval(() => {
                        this.fetchConversations(false);

                        if (this.activeConversation) {
                            this.fetchMessages(true);
                        }
                    }, 3000);

                    window.addEventListener('beforeunload', () => {
                        if (this.pollingInterval) {
                            clearInterval(this.pollingInterval);
                        }
                    });
                },

                readBootcampContext() {
                    const params = new URLSearchParams(window.location.search);
                    if (params.get('source') !== 'bootcamp') {
                        return;
                    }

                    const sessionTitle = params.get('session_title');
                    const batch = params.get('batch');

                    this.bootcampContext = {
                        label: sessionTitle
                            ? `Sesi: ${sessionTitle}${batch ? ` | Batch: ${batch}` : ''}`
                            : `Masuk dari menu bootcamp${batch ? ` | Batch: ${batch}` : ''}`,
                    };
                },

                async fetchConversations(showLoading = true) {
                    if (showLoading) {
                        this.isLoadingConversations = true;
                    }

                    try {
                        const response = await fetch(`/dosen/messages/conversations?search=${encodeURIComponent(this.searchQuery)}`, {
                            credentials: 'same-origin',
                            headers: { Accept: 'application/json' },
                        });
                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.conversations = (data.data || []).map((conv) => ({
                                student_id: conv.student_id,
                                student_name: conv.student_name,
                                student_nomor_induk: conv.student_nomor_induk || '-',
                                student_email: conv.student_email || '-',
                                student_avatar: conv.student_avatar,
                                last_message: conv.last_message,
                                last_message_time: conv.last_message_time,
                                unread_count: conv.unread_count || 0,
                            }));

                            if (this.activeConversation) {
                                const updatedConversation = this.conversations.find(
                                    (conv) => conv.student_id === this.activeConversation.student_id
                                );

                                if (updatedConversation) {
                                    this.activeConversation = updatedConversation;
                                } else {
                                    this.activeConversation = null;
                                    this.messages = [];
                                    this.showStudentProfile = false;
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Error fetching conversations:', error);
                    } finally {
                        if (showLoading) {
                            this.isLoadingConversations = false;
                        }
                    }
                },

                async selectConversation(conv) {
                    this.activeConversation = conv;
                    this.showStudentProfile = false;

                    const conversation = this.conversations.find((item) => item.student_id === conv.student_id);
                    if (conversation) {
                        conversation.unread_count = 0;
                    }

                    await this.fetchMessages();
                },

                async fetchMessages(isPolling = false) {
                    if (!this.activeConversation) {
                        return;
                    }

                    if (!isPolling) {
                        this.isLoadingMessages = true;
                        this.messages = [];
                    }

                    try {
                        const response = await fetch(`/dosen/messages/chat/${this.activeConversation.student_id}`, {
                            credentials: 'same-origin',
                            headers: { Accept: 'application/json' },
                        });
                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.messages = data.data || [];
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    } catch (error) {
                        console.error('Error fetching messages:', error);
                    } finally {
                        if (!isPolling) {
                            this.isLoadingMessages = false;
                        }
                    }
                },

                async sendMessage() {
                    const messageToSend = this.newMessage.trim();
                    if (!messageToSend || !this.activeConversation) {
                        return;
                    }

                    this.isSending = true;

                    const tempMessage = {
                        id: `temp-${Date.now()}`,
                        content: messageToSend,
                        sender_type: 'dosen',
                        created_at: new Date().toISOString(),
                    };

                    this.messages.push(tempMessage);
                    this.newMessage = '';
                    this.$nextTick(() => this.scrollToBottom());

                    try {
                        const response = await fetch('/dosen/messages/send', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify({
                                student_id: this.activeConversation.student_id,
                                content: messageToSend,
                            }),
                        });
                        const data = await response.json();

                        if (response.ok && data.success) {
                            await this.fetchMessages(true);
                            await this.fetchConversations(false);
                        } else {
                            this.messages = this.messages.filter((message) => message.id !== tempMessage.id);
                            this.newMessage = messageToSend;
                            alert(data.message || 'Gagal mengirim pesan.');
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                        this.messages = this.messages.filter((message) => message.id !== tempMessage.id);
                        this.newMessage = messageToSend;
                        alert('Gagal mengirim pesan. Silakan coba lagi.');
                    } finally {
                        this.isSending = false;
                    }
                },

                scrollToBottom() {
                    const container = document.getElementById('messages-container');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                },

                formatTime(iso) {
                    if (!iso) {
                        return '';
                    }

                    try {
                        const date = new Date(iso);
                        if (Number.isNaN(date.getTime())) {
                            return '';
                        }

                        const now = new Date();
                        if (date.toDateString() === now.toDateString()) {
                            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                        }

                        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                    } catch (error) {
                        return '';
                    }
                },
            }));
        });
    </script>
    @endpush
</x-layouts.dosen>

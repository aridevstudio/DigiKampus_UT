<x-layouts.dashboard :active="'chat'">
    <div x-data="mahasiswaChatSystem()" x-init="initChat()" class="h-[calc(100vh-120px)] flex flex-col">
        <div class="mb-4 hidden flex-shrink-0 md:block">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chat</h1>
            <p class="mt-1 text-gray-500 dark:text-gray-400">Komunikasi langsung dengan dosen pengampu Anda.</p>
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
                            placeholder="Cari dosen..."
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada dosen yang bisa dihubungi.</p>
                </div>

                <div x-show="!isLoadingConversations && conversations.length > 0" class="flex-1 overflow-y-auto">
                    <template x-for="conv in conversations" :key="conv.dosen_id">
                        <button type="button"
                                @click="selectConversation(conv)"
                                class="block w-full cursor-pointer border-l-4 px-3 py-3 text-left transition"
                                :class="activeConversation && activeConversation.dosen_id === conv.dosen_id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-transparent hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                            <div class="flex items-center gap-3">
                                <div class="relative flex-shrink-0">
                                    <img :src="conv.dosen_avatar" :alt="conv.dosen_name" class="h-10 w-10 rounded-full bg-gray-200 object-cover">
                                    <span x-show="conv.unread_count > 0" class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full border-2 border-white bg-blue-500 text-xs font-bold text-white dark:border-gray-800" x-text="conv.unread_count"></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="mb-1 flex items-center justify-between gap-2">
                                        <p class="truncate text-sm font-semibold text-gray-900 dark:text-white" x-text="conv.dosen_name"></p>
                                        <span class="text-xs text-gray-400" x-text="formatTime(conv.last_message_time)"></span>
                                    </div>
                                    <p class="truncate text-xs text-gray-500 dark:text-gray-400" x-text="conv.last_message || 'Mulai percakapan'"></p>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Pilih Percakapan</h3>
                    <p class="max-w-sm text-gray-500 dark:text-gray-400">Pilih dosen dari daftar kiri untuk mulai chat realtime.</p>
                </div>

                <div x-show="activeConversation" class="flex h-full flex-1 flex-col overflow-hidden">
                    <div class="flex flex-shrink-0 items-center gap-3 border-b border-gray-100 bg-white px-5 py-3 dark:border-gray-700 dark:bg-gray-800">
                        <button @click="activeConversation = null; messages = []" class="-ml-2 rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        <img :src="activeConversation?.dosen_avatar" :alt="activeConversation?.dosen_name" class="h-10 w-10 rounded-full bg-gray-200 object-cover">
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-gray-900 dark:text-white" x-text="activeConversation?.dosen_name"></p>
                            <p class="truncate text-xs text-gray-500 dark:text-gray-400" x-text="activeConversation?.dosen_email"></p>
                            <p class="mt-0.5 text-[10px] font-medium text-emerald-500 dark:text-emerald-400">Chat realtime aktif</p>
                        </div>
                    </div>

                    <div id="messages-container" class="flex-1 space-y-4 overflow-y-auto bg-gray-50/50 p-5 scroll-smooth dark:bg-gray-900/30">
                        <div x-show="isLoadingMessages" class="flex justify-center py-4">
                            <svg class="h-6 w-6 animate-spin text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>

                        <div x-show="!isLoadingMessages && messages.length === 0" class="py-10 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada pesan. Mulai percakapan dengan dosen ini.</p>
                        </div>

                        <template x-for="msg in messages" :key="msg.id">
                            <div class="flex max-w-[88%] items-end gap-2 sm:max-w-[82%] md:max-w-[75%]"
                                 :class="msg.sender_type === 'mahasiswa' ? 'ml-auto flex-row-reverse' : ''">
                                <template x-if="msg.sender_type === 'admin'">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 text-[11px] font-semibold text-red-700">
                                        AD
                                    </div>
                                </template>
                                <template x-if="msg.sender_type !== 'mahasiswa' && msg.sender_type !== 'admin'">
                                    <img :src="activeConversation?.dosen_avatar" :alt="activeConversation?.dosen_name" class="h-8 w-8 rounded-full object-cover">
                                </template>
                                <div>
                                    <span x-show="msg.sender_type === 'admin'" class="mb-1 block text-[11px] font-semibold text-red-600">Admin DigiKampus</span>
                                    <div class="rounded-2xl px-4 py-2.5 text-sm shadow-sm"
                                         :class="msg.sender_type === 'mahasiswa'
                                             ? 'rounded-br-sm bg-blue-500 text-white'
                                             : (msg.sender_type === 'admin'
                                                 ? 'rounded-bl-sm border border-red-200 bg-red-50 text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-200'
                                                 : 'rounded-bl-sm bg-white text-gray-700 dark:bg-gray-800 dark:text-gray-300')">
                                        <p class="whitespace-pre-wrap break-words" x-text="msg.content"></p>
                                    </div>
                                    <span class="mt-1 block text-[10px] text-gray-400"
                                          :class="msg.sender_type === 'mahasiswa' ? 'text-right' : 'text-left'"
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
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mahasiswaChatSystem', () => ({
                conversations: [],
                messages: [],
                activeConversation: null,
                searchQuery: '',
                newMessage: '',
                isLoadingConversations: false,
                isLoadingMessages: false,
                isSending: false,
                pollingInterval: null,

                initChat() {
                    this.fetchConversations();

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

                async fetchConversations(showLoading = true) {
                    if (showLoading) {
                        this.isLoadingConversations = true;
                    }

                    try {
                        const response = await fetch(`/mahasiswa/messages/conversations?search=${encodeURIComponent(this.searchQuery)}`, {
                            credentials: 'same-origin',
                            headers: { Accept: 'application/json' },
                        });
                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.conversations = data.data || [];

                            if (this.activeConversation) {
                                const updatedConversation = this.conversations.find(
                                    (conv) => conv.dosen_id === this.activeConversation.dosen_id
                                );

                                if (updatedConversation) {
                                    this.activeConversation = updatedConversation;
                                } else {
                                    this.activeConversation = null;
                                    this.messages = [];
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

                    const conversation = this.conversations.find((item) => item.dosen_id === conv.dosen_id);
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
                        const response = await fetch(`/mahasiswa/messages/chat/${this.activeConversation.dosen_id}`, {
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
                        sender_type: 'mahasiswa',
                        created_at: new Date().toISOString(),
                    };

                    this.messages.push(tempMessage);
                    this.newMessage = '';
                    this.$nextTick(() => this.scrollToBottom());

                    try {
                        const response = await fetch('/mahasiswa/messages/send', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify({
                                dosen_id: this.activeConversation.dosen_id,
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
</x-layouts.dashboard>

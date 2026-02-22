<x-layouts.dosen title="Pesan" active="pesan">
    {{-- Main Content with Alpine.js Data --}}
    <div x-data="chatSystem()" x-init="initChat()" class="h-[calc(100vh-120px)] flex flex-col">
        {{-- Header & Introduction (Visible only on desktop/large screens) --}}
        <div class="mb-4 flex-shrink-0 hidden md:block">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pesan</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Komunikasi dengan mahasiswa dalam satu tempat.</p>
        </div>

        {{-- Chat Container --}}
        <div class="flex-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden flex">
            
            {{-- Left Sidebar - Conversation List --}}
            <div class="w-full md:w-80 border-r border-gray-100 dark:border-gray-700 flex flex-col" 
                 :class="{'hidden md:flex': activeConversation, 'flex': !activeConversation}">
                
                {{-- Search --}}
                <div class="p-3 border-b border-gray-100 dark:border-gray-700">
                    <div class="relative">
                        <input x-model="searchQuery" @input.debounce.500ms="fetchConversations()" type="text" placeholder="Cari mahasiswa..." class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
                
                {{-- Loading State --}}
                <div x-show="isLoadingConversations" class="flex-1 flex items-center justify-center">
                    <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                {{-- Empty State --}}
                <div x-show="!isLoadingConversations && conversations.length === 0" class="flex-1 flex flex-col items-center justify-center p-4 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada pesan</p>
                </div>

                {{-- Conversation List --}}
                <div x-show="!isLoadingConversations && conversations.length > 0" class="flex-1 overflow-y-auto">
                    <template x-for="conv in conversations" :key="conv.student_id">
                        <div @click="selectConversation(conv)" 
                             class="px-3 py-3 cursor-pointer transition border-l-4"
                             :class="activeConversation && activeConversation.student_id === conv.student_id ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50 border-transparent'">
                            <div class="flex items-center gap-3">
                                <div class="relative flex-shrink-0">
                                    <img :src="conv.student_avatar" :alt="conv.student_name" class="w-10 h-10 rounded-full object-cover bg-gray-200">
                                    <span x-show="conv.unread_count > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-blue-500 text-white text-xs font-bold rounded-full flex items-center justify-center border-2 border-white dark:border-gray-800" x-text="conv.unread_count"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="font-semibold text-gray-900 dark:text-white text-sm truncate" x-text="conv.student_name"></p>
                                        <span class="text-xs text-gray-400" x-text="formatTime(conv.last_message_time)"></span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate" x-text="conv.last_message"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Center - Chat Area --}}
            <div class="flex-1 flex flex-col bg-gray-50/30 dark:bg-gray-900/10" 
                 :class="{'flex': activeConversation, 'hidden md:flex': !activeConversation}">
                
                {{-- No Conversation Selected --}}
                <div x-show="!activeConversation" class="flex-1 flex flex-col items-center justify-center text-center p-8">
                    <div class="w-24 h-24 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Pilih Percakapan</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm">Pilih mahasiswa dari daftar di sebelah kiri untuk mulai mengirim pesan.</p>
                </div>

                {{-- Active Chat Interface --}}
                <div x-show="activeConversation" class="flex-1 flex flex-col h-full overflow-hidden">
                    {{-- Chat Header --}}
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-center justify-between flex-shrink-0">
                        <div class="flex items-center gap-3">
                            {{-- Back Button (Mobile) --}}
                            <button @click="activeConversation = null" class="md:hidden p-2 -ml-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            
                            <img :src="activeConversation?.student_avatar" class="w-10 h-10 rounded-full object-cover bg-gray-200">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white" x-text="activeConversation?.student_name"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="activeConversation?.student_nim"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="showStudentProfile = !showStudentProfile" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition" title="Lihat Profil">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Messages List --}}
                    <div id="messages-container" class="flex-1 overflow-y-auto p-5 space-y-4 bg-gray-50/50 dark:bg-gray-900/30 scroll-smooth">
                        <div x-show="isLoadingMessages" class="flex justify-center py-4">
                            <svg class="animate-spin h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        
                        <template x-for="(msg, index) in messages" :key="msg.id">
                            <div class="flex flex-col">
                                {{-- Date Separator (Optional logic could go here) --}}
                                
                                <div class="flex items-end gap-2 max-w-[85%] md:max-w-[75%]" 
                                     :class="msg.sender_type === 'dosen' ? 'ml-auto flex-row-reverse' : ''">
                                    
                                    <template x-if="msg.sender_type !== 'dosen'">
                                        <img :src="activeConversation?.student_avatar" class="w-8 h-8 rounded-full object-cover">
                                    </template>
                                    
                                    <div>
                                        <div class="px-4 py-2.5 rounded-2xl shadow-sm text-sm break-words"
                                             :class="msg.sender_type === 'dosen' 
                                                ? 'bg-blue-500 text-white rounded-br-sm' 
                                                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-bl-sm'">
                                            <p x-text="msg.content" class="whitespace-pre-wrap"></p>
                                        </div>
                                        <span class="text-[10px] text-gray-400 mt-1 block" 
                                              :class="msg.sender_type === 'dosen' ? 'text-right' : 'text-left'"
                                              x-text="formatTime(msg.created_at)"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Message Input --}}
                    <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 flex-shrink-0">
                        <form @submit.prevent="sendMessage" class="flex items-end gap-3">
                            <div class="flex-1 relative">
                                <textarea x-model="newMessage" 
                                          @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                                          placeholder="Ketik pesan..." 
                                          rows="1"
                                          class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 resize-none max-h-32"
                                          style="min-height: 44px;"></textarea>
                            </div>
                            <button type="submit" 
                                    :disabled="!newMessage.trim() || isSending"
                                    class="p-3 bg-blue-500 hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl transition shadow-sm mb-0.5">
                                <svg x-show="!isSending" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <svg x-show="isSending" class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right Sidebar - Profile Details --}}
            <div x-show="activeConversation && showStudentProfile" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 class="w-80 border-l border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-y-auto absolute md:relative right-0 h-full z-10 shadow-xl md:shadow-none"
                 style="display: none;">
                
                <div class="p-4 flex justify-end md:hidden">
                    <button @click="showStudentProfile = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-5 text-center border-b border-gray-100 dark:border-gray-700">
                    <img :src="activeConversation?.student_avatar" class="w-20 h-20 rounded-full mx-auto mb-3 bg-gray-200 object-cover">
                    <h3 class="font-bold text-gray-900 dark:text-white" x-text="activeConversation?.student_name"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'NIM: ' + activeConversation?.student_nim"></p>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-medium mb-1">Email</p>
                        <p class="text-sm text-blue-500 truncate" x-text="activeConversation?.student_email"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('chatSystem', () => ({
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

                initChat() {
                    this.fetchConversations();
                    // Poll for new messages every 10 seconds
                    this.pollingInterval = setInterval(() => {
                        this.fetchConversations(false); // Silent update for list
                        if (this.activeConversation) {
                            this.fetchMessages(true); // Silent update for chat
                        }
                    }, 10000);
                },

                async fetchConversations(showLoading = true) {
                    if (showLoading) this.isLoadingConversations = true;
                    try {
                        const response = await fetch(`/api/dosen/messages?search=${this.searchQuery}`, {
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.conversations = data.data.map(conv => ({
                                student_id: conv.student_id,
                                student_name: conv.student_name,
                                student_nim: conv.student_nim || '-',
                                student_email: conv.student_email || '-',
                                student_avatar: conv.student_avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(conv.student_name)}&background=random`,
                                last_message: conv.last_message,
                                last_message_time: conv.last_message_time,
                                unread_count: conv.unread_count
                            }));
                        }
                    } catch (error) {
                        console.error('Error fetching conversations:', error);
                    } finally {
                        if (showLoading) this.isLoadingConversations = false;
                    }
                },

                async selectConversation(conv) {
                    this.activeConversation = conv;
                    this.showStudentProfile = false;
                    this.fetchMessages();
                    
                    // Mark locally as read
                    const convIndex = this.conversations.findIndex(c => c.student_id === conv.student_id);
                    if (convIndex !== -1) {
                        this.conversations[convIndex].unread_count = 0;
                    }
                },

                async fetchMessages(isPolling = false) {
                    if (!this.activeConversation) return;
                    if (!isPolling) {
                        this.isLoadingMessages = true;
                        this.messages = [];
                    }

                    try {
                        const response = await fetch(`/api/dosen/messages/${this.activeConversation.student_id}`, {
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            // If polling, only append new messages or replace if structure allows
                            // For simplicity, we assume replacing is fine for now, but in prod we'd merge
                            this.messages = data.data; 
                            
                            if (!isPolling) {
                                this.$nextTick(() => {
                                    this.scrollToBottom();
                                });
                            }
                        }
                    } catch (error) {
                        console.error('Error fetching messages:', error);
                    } finally {
                        if (!isPolling) this.isLoadingMessages = false;
                    }
                },

                async sendMessage() {
                    if (!this.newMessage.trim() || !this.activeConversation) return;

                    this.isSending = true;
                    const tempMessage = {
                        id: 'temp-' + Date.now(),
                        content: this.newMessage,
                        sender_type: 'dosen',
                        created_at: new Date().toISOString(),
                        is_sending: true
                    };

                    // Optimistic UI update
                    this.messages.push(tempMessage);
                    const messageToSend = this.newMessage;
                    this.newMessage = '';
                    this.$nextTick(() => this.scrollToBottom());

                    try {
                        const response = await fetch('/api/dosen/messages', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                student_id: this.activeConversation.student_id,
                                content: messageToSend
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Replace temp message with real one or just refetch
                            this.fetchMessages(true);
                            this.fetchConversations(false); // Update last message in sidebar
                        } else {
                            // Revert on failure
                            this.messages = this.messages.filter(m => m.id !== tempMessage.id);
                            this.newMessage = messageToSend; // Restore text
                            alert('Gagal mengirim pesan: ' + data.message);
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                        this.messages = this.messages.filter(m => m.id !== tempMessage.id);
                        this.newMessage = messageToSend;
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

                formatTime(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    const now = new Date();
                    const isToday = date.toDateString() === now.toDateString();
                    
                    if (isToday) {
                        return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    }
                    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                }
            }));
        });
    </script>
    @endpush
</x-layouts.dosen>

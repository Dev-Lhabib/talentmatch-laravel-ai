@props([
    "application",
    "messages" => collect(),
    "chatStoreUrl" => "#",
])

@php
    $candidate = $application->candidate;
    $messagesJson = $messages->map(fn ($m) => [
        "role" => $m->role instanceof \App\Enums\MessageRoleEnum ? $m->role->value : $m->role,
        "content" => $m->content,
        "label" => ($m->role instanceof \App\Enums\MessageRoleEnum ? $m->role->value : $m->role) === "user" ? "HR Agent" : "AI",
    ])->values()->toJson();
@endphp

<div class="flex h-full flex-col rounded-xl border border-border bg-card"
     x-data="{
        messages: {{ $messagesJson }},
        message: '',
        sending: false,
        async send() {
            if (this.message.trim() === '' || this.sending) return;
            const msg = this.message;
            this.messages.push({ role: 'user', content: msg, label: 'HR Agent' });
            this.message = '';
            this.sending = true;
            try {
                const response = await fetch('{{ $chatStoreUrl }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message: msg }),
                });
                if (!response.ok) {
                    const err = await response.json();
                    this.messages.push({ role: 'assistant', content: err.error || 'Erreur', label: 'AI' });
                    return;
                }
                const data = await response.json();
                this.messages.push({ role: 'assistant', content: data.content, label: 'AI' });
            } catch (e) {
                console.error('Chat error:', e);
                this.messages.push({ role: 'assistant', content: 'Erreur de connexion.', label: 'AI' });
            } finally {
                this.sending = false;
                this.$nextTick(() => {
                    const el = this.$refs.thread;
                    if (el) el.scrollTop = el.scrollHeight;
                });
            }
        }
     }">

    {{-- Panel Header --}}
    <div class="flex items-center justify-between border-b border-border px-5 py-3">
        <h2 class="text-sm font-semibold text-white">AI Assistant</h2>
    </div>

    {{-- Chat Thread --}}
    <div x-ref="thread" class="flex-1 overflow-y-auto px-5 py-4">
        <template x-if="messages.length === 0">
            <div class="flex h-full items-center justify-center">
                <p class="text-sm text-text-secondary">Posez une question sur <span x-text="'{{ $candidate->name }}'"></span>...</p>
            </div>
        </template>
        <div class="space-y-4">
            <template x-for="(msg, i) in messages" :key="i">
                <div class="flex items-start gap-3">
                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full"
                         :class="msg.role === 'user' ? 'bg-accent' : 'bg-teal'">
                        <template x-if="msg.role === 'user'">
                            <span class="text-xs font-bold text-white" x-text="msg.label.charAt(0)"></span>
                        </template>
                        <template x-if="msg.role !== 'user'">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </template>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="mb-1 text-xs font-semibold text-white" x-text="msg.label"></p>
                        <div class="rounded-lg rounded-tl-none bg-card px-4 py-2.5 text-sm leading-relaxed text-text-secondary border border-border" x-text="msg.content"></div>
                    </div>
                </div>
            </template>
            <template x-if="sending">
                <div class="flex items-start gap-3">
                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-teal">
                        <svg class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="mb-1 text-xs font-semibold text-white">AI</p>
                        <div class="rounded-lg rounded-tl-none bg-card px-4 py-2.5 text-sm text-text-secondary border border-border">
                            <span class="animate-pulse">Réflexion...</span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="flex flex-wrap gap-2 border-t border-border px-5 py-2">
        <button type="button" class="rounded bg-card border border-border px-2.5 py-1 text-xs text-text-secondary transition hover:bg-card-hover hover:text-white"
            @click="message = 'Pourquoi ce score ?'; $nextTick(() => { const inp = document.querySelector('#chat-input'); if(inp) inp.focus(); })">
            Pourquoi ce score ?
        </button>
        <button type="button" class="rounded bg-card border border-border px-2.5 py-1 text-xs text-text-secondary transition hover:bg-card-hover hover:text-white"
            @click="message = 'Quelles questions poser en entretien ?'; $nextTick(() => { const inp = document.querySelector('#chat-input'); if(inp) inp.focus(); })">
            Questions entretien ?
        </button>
        <button type="button" class="rounded bg-card border border-border px-2.5 py-1 text-xs text-text-secondary transition hover:bg-card-hover hover:text-white"
            @click="message = 'Quels sont ses points faibles ?'; $nextTick(() => { const inp = document.querySelector('#chat-input'); if(inp) inp.focus(); })">
            Points faibles ?
        </button>
    </div>

    {{-- Input Bar --}}
    <div class="border-t border-border px-5 py-3">
        <div class="flex items-center gap-2">
            <input
                id="chat-input"
                type="text"
                x-model="message"
                @keydown.enter.prevent="send()"
                placeholder="Poser une question..."
                class="flex-1 rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal"
            >
            <button
                @click="send()"
                :disabled="sending"
                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-teal text-white transition hover:bg-teal/80 disabled:opacity-50"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </button>
        </div>
    </div>
</div>

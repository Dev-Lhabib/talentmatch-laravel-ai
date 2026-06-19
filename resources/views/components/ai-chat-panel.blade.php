@props([
    "candidate",
    "messages" => collect(),
    "chatStoreUrl" => "#",
])

<div class="flex h-full flex-col rounded-xl border border-border bg-card"
     x-data="{ message: "", sending: false, async send() { if (this.message.trim() === "" || this.sending) return; this.sending = true; const msg = this.message; this.message = ""; try { const response = await fetch("{{ $chatStoreUrl }}", { method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content, "Accept": "text/html" }, body: JSON.stringify({ message: msg }) }); if (response.redirected) { window.location.href = response.url; } else if (response.ok) { window.location.reload(); } } catch (e) { console.error("Chat error:", e); } finally { this.sending = false; } } }">

    {{-- Panel Header --}}
    <div class="flex items-center justify-between border-b border-border px-5 py-3">
        <h2 class="text-sm font-semibold text-white">AI Assistant</h2>
        <button class="p-1 text-text-secondary transition hover:text-white">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Chat Thread --}}
    <div class="flex-1 overflow-y-auto px-5 py-4">
        @if($messages->isEmpty())
            <div class="flex h-full items-center justify-center">
                <p class="text-sm text-text-secondary">Posez une question sur {{ $candidate->name }}...</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($messages as $msg)
                    <x-chat-message
                        :role="$msg->role === "user" ? "user" : "assistant""
                        :content="$msg->content"
                        :label="$msg->role === "user" ? "HR Agent" : "AI""
                    />
                @endforeach
            </div>
        @endif
    </div>

    {{-- Input Bar --}}
    <div class="border-t border-border px-5 py-3">
        <div class="flex items-center gap-2">
            <button class="flex-shrink-0 p-2 text-text-secondary transition hover:text-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
            </button>

            <input
                type="text"
                x-model="message"
                @keydown.enter.prevent="send()"
                placeholder="Ask something about {{ $candidate->name }}..."
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

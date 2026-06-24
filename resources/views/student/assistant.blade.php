@extends('layouts.app')

@section('title', 'Assistente Estudaí - IA')

@section('content')
<div class="assistant-layout">
    
    <!-- Left Panel: Recommendations list -->
    <div class="assistant-left">
        <h3 class="section-title" style="margin-bottom: 1rem;">
            <i class="fa-solid fa-list-check" style="color: var(--primary);"></i> Suas Recomendações
        </h3>
        <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1.5rem;">
            Selecione uma recomendação abaixo para pedir explicações detalhadas ou exemplos práticos para o assistente de IA.
        </p>

        <div class="list-wrapper">
            @if($recommendations->count() > 0)
                @foreach($recommendations as $rec)
                    @php
                        // Banco de dados (topic_id 1) e Prog Web (topic_id 4) são prioritários
                        $isPriority = $rec->topic_id == 1 || $rec->topic_id == 4;
                    @endphp
                    <div class="rec-item" style="cursor: pointer;" onclick="askAboutRecommendation({{ $rec->topic_id }}, '{{ addslashes($rec->content->titulo) }}')">
                        <div class="rec-content">
                            @if($isPriority)
                                <span class="rec-badge badge-prioritario">Prioritário</span>
                            @else
                                <span class="rec-badge badge-revisao">Revisão Leve</span>
                            @endif
                            <div class="rec-info">
                                <h4 style="font-size: 0.95rem;">{{ $rec->content->titulo }}</h4>
                                <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.1rem;">
                                    Tópico: <strong>{{ $rec->topic->nome }}</strong>
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <button type="button" class="btn btn-primary" style="font-size: 0.75rem; padding: 0.4rem 0.7rem;">
                                <i class="fa-solid fa-comments"></i> Perguntar
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card" style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                    <i class="fa-solid fa-circle-check" style="font-size: 2rem; color: var(--success); margin-bottom: 0.8rem;"></i>
                    <p>Você não tem recomendações pendentes para perguntar!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Panel: Chatbot Console -->
    <div class="assistant-right">
        <!-- Chat Header -->
        <div class="chat-header">
            <div class="chat-avatar">
                <i class="fa-solid fa-robot"></i>
            </div>
            <div class="chat-user-status">
                <strong style="font-size: 1.05rem;">Assistente Estudaí</strong>
                <span style="font-size: 0.8rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.3rem;">
                    <span class="chat-status-dot"></span> Online — Pronto para ajudar
                </span>
            </div>
        </div>

        <!-- Messages Box -->
        <div class="chat-messages" id="chat-messages-container">
            <!-- Initial chatbot greeting message -->
            <div class="chat-bubble chat-bubble-received">
                <i class="fa-solid fa-robot" style="color: var(--primary);"></i> Olá! Sou o <strong>Assistente Estudaí</strong>.
                <br><br>
                Analisei seu histórico de desempenho e identifiquei algumas áreas que precisam de atenção. Você pode clicar em <strong>"Perguntar"</strong> em qualquer recomendação ao lado, ou digitar sua dúvida diretamente na barra abaixo!
            </div>
        </div>

        <!-- Chat Input Bar -->
        <div class="chat-input-bar">
            <!-- Hidden field to send context topic -->
            <input type="hidden" id="chat-context-topic" value="">
            
            <input type="text" id="chat-user-input" class="chat-input" placeholder="Pergunte algo sobre os conteúdos recomendados..." onkeypress="handleKeyPress(event)">
            
            <button type="button" id="chat-send-btn" class="chat-send-btn" onclick="sendUserMessage()">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    const messagesContainer = document.getElementById('chat-messages-container');
    const userInputField = document.getElementById('chat-user-input');
    const contextTopicField = document.getElementById('chat-context-topic');

    // Auto-scroll to bottom of chat
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Handle Enter Key press in Input
    function handleKeyPress(e) {
        if (e.key === 'Enter') {
            sendUserMessage();
        }
    }

    // Send Message
    function sendUserMessage() {
        const messageText = userInputField.value.trim();
        if (messageText === "") return;

        // Display user message in chat
        appendMessage(messageText, 'sent');
        userInputField.value = ""; // Clear input

        // Show typing indicator
        const typingIndicator = appendTypingIndicator();
        scrollToBottom();

        // Get context topic if set
        const topicId = contextTopicField.value;

        // Send AJAX request to controller
        fetch("{{ route('assistant.message') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                message: messageText,
                topic_id: topicId ? parseInt(topicId) : null
            })
        })
        .then(response => response.json())
        .then(data => {
            // Remove typing indicator
            typingIndicator.remove();
            
            // Format markdown-like code blocks if present
            let replyText = formatReplyText(data.reply);
            
            // Append assistant reply
            appendMessage(replyText, 'received', true);
            scrollToBottom();
        })
        .catch(error => {
            typingIndicator.remove();
            appendMessage("Desculpe, ocorreu um erro de conexão. Tente novamente mais tarde.", 'received');
            scrollToBottom();
            console.error('Error:', error);
        });
    }

    // Helper to append message bubbles
    function appendMessage(text, type, isHTML = false) {
        const bubble = document.createElement('div');
        bubble.className = `chat-bubble chat-bubble-${type}`;
        
        if (isHTML) {
            bubble.innerHTML = text;
        } else {
            bubble.textContent = text;
        }
        
        messagesContainer.appendChild(bubble);
        scrollToBottom();
        return bubble;
    }

    // Helper to show typing indicator
    function appendTypingIndicator() {
        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble chat-bubble-received';
        bubble.id = 'typing-indicator';
        bubble.innerHTML = '<i class="fa-solid fa-ellipsis fa-bounce"></i> Assistente está digitando...';
        messagesContainer.appendChild(bubble);
        return bubble;
    }

    // Helper to format code blocks and newlines to html
    function formatReplyText(text) {
        // Convert markdown bold **text** to HTML <strong>text</strong>
        let html = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        // Convert code blocks ```sql ... ```
        html = html.replace(/```(sql|javascript|css)?([\s\S]*?)```/g, function(match, lang, code) {
            return `<pre style="background-color: var(--bg-primary); border: 1px solid var(--border-color); padding: 1rem; border-radius: 8px; font-family: monospace; overflow-x: auto; margin: 1rem 0; font-size: 0.85rem; color: #a5b4fc;"><code class="language-${lang || ''}">${code.trim()}</code></pre>`;
        });

        // Convert inline code `code`
        html = html.replace(/`(.*?)`/g, '<code style="background-color: var(--bg-primary); padding: 0.1rem 0.3rem; border-radius: 4px; font-family: monospace; color: #f472b6;">$1</code>');

        // Convert newlines to break tags, except inside <pre> blocks
        // Simple line break replace
        html = html.replace(/\n/g, '<br>');
        
        // Fix double break tags created by formatting pre blocks
        html = html.replace(/<\/pre><br>/g, '</pre>');
        
        return html;
    }

    // Ask about recommendation click
    function askAboutRecommendation(topicId, contentTitle) {
        contextTopicField.value = topicId;
        userInputField.value = `Pode me explicar sobre o conteúdo "${contentTitle}"?`;
        sendUserMessage();
    }

    // Auto-trigger if prefilledRec is passed from redirect
    @if(isset($prefilledRec))
        window.addEventListener('load', () => {
            askAboutRecommendation({{ $prefilledRec->topic_id }}, '{{ addslashes($prefilledRec->content->titulo) }}');
        });
    @endif
</script>
@endsection

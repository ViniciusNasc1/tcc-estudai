<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Recommendation;
use App\Models\QuizResult;
use App\Models\TopicPerformance;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function home()
    {
        $user = Auth::user();
        
        // Carrega tópicos e o desempenho correspondente
        $topics = Topic::with(['contents', 'quizzes'])->get();
        
        $performances = TopicPerformance::where('user_id', $user->id)
            ->get()
            ->keyBy('topic_id');
            
        // Recomendações pendentes
        $recommendations = Recommendation::where('user_id', $user->id)
            ->where('status', 'pendente')
            ->with(['content', 'topic'])
            ->get();
            
        // Últimos quizzes realizados
        $latestResults = QuizResult::where('user_id', $user->id)
            ->with('quiz.topic')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('student.home', compact('user', 'topics', 'performances', 'recommendations', 'latestResults'));
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Estatísticas
        $quizzesRealizados = QuizResult::where('user_id', $user->id)->count();
        
        $mediaGeral = QuizResult::where('user_id', $user->id)->avg('pontuacao_percentual') ?? 0;
        
        $recomendacoesPendentes = Recommendation::where('user_id', $user->id)
            ->where('status', 'pendente')
            ->count();
            
        // Conteúdos vistos simulados (ex: 18 baseados no PDF)
        $conteudosVistos = 18;

        // Desempenho por tópico para o gráfico de barras
        $topicPerformances = TopicPerformance::where('user_id', $user->id)
            ->with('topic')
            ->get();

        // Evolução de quizzes para o gráfico de linha
        $evolutionResults = QuizResult::where('user_id', $user->id)
            ->with('quiz')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('student.dashboard', compact(
            'quizzesRealizados', 
            'mediaGeral', 
            'recomendacoesPendentes', 
            'conteudosVistos',
            'topicPerformances',
            'evolutionResults'
        ));
    }

    public function assistant(Request $request)
    {
        $user = Auth::user();

        $recommendations = Recommendation::where('user_id', $user->id)
            ->where('status', 'pendente')
            ->with(['content', 'topic'])
            ->get();

        // Se veio de um clique de "Perguntar à IA", pegamos a recomendação específica
        $prefilledRecId = $request->query('rec_id');
        $prefilledRec = null;
        if ($prefilledRecId) {
            $prefilledRec = Recommendation::where('user_id', $user->id)
                ->with(['content', 'topic'])
                ->find($prefilledRecId);
        }

        return view('student.assistant', compact('recommendations', 'prefilledRec'));
    }

    public function message(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'topic_id' => 'nullable|integer',
        ]);

        $message = trim($request->message);
        $topicId = $request->topic_id;
        $response = "";

        // Se contiver palavras relacionadas a SQL/Banco de dados ou for selecionado esse tópico
        if ($topicId == 1 || stripos($message, 'sql') !== false || stripos($message, 'join') !== false || stripos($message, 'select') !== false || stripos($message, 'banco') !== false) {
            $response = "Olá! Notei que você está revisando **Banco de Dados**, especialmente as **Consultas SQL**. 

O comando `SELECT` é a base de tudo no SQL. Para filtrar registros, usamos a cláusula `WHERE`. E quando precisamos unir dados de duas tabelas diferentes, usamos o `JOIN` (como `INNER JOIN` ou `LEFT JOIN`).

Aqui está um exemplo prático de consulta relacional:
```sql
SELECT usuarios.nome, resultados.pontuacao_percentual
FROM usuarios
INNER JOIN resultados ON usuarios.id = resultados.usuario_id
WHERE resultados.pontuacao_percentual < 80;
```
Esse comando busca o nome dos usuários e as notas onde o desempenho foi menor do que 80%.

Ficou clara essa estrutura? Gostaria que eu explicasse algum tipo específico de JOIN ou outro comando SQL?";
        } 
        // Se contiver palavras relacionadas a Redes ou Modelo OSI
        elseif ($topicId == 3 || stripos($message, 'osi') !== false || stripos($message, 'redes') !== false || stripos($message, 'camada') !== false || stripos($message, 'tcp') !== false) {
            $response = "Olá! Analisei seu desempenho e vi que você está com dúvidas sobre o **Modelo OSI**. 

O Modelo OSI é composto por 7 camadas fundamentais:
1. **Física**: Cabos, fibras ópticas e sinais elétricos.
2. **Enlace**: Endereçamento físico (MAC) e switches.
3. **Rede**: Endereçamento lógico (IP) e roteamento de pacotes.
4. **Transporte**: Controle de fluxo e confiabilidade (TCP/UDP) e portas.
5. **Sessão**: Estabelecimento e controle de conexões de sessão.
6. **Apresentação**: Formatação de dados, compressão e criptografia.
7. **Aplicação**: Interface direta com o usuário final (HTTP, DNS, SMTP, FTP).

Lembre-se: o protocolo **HTTP** opera na camada de **Aplicação** (7ª), enquanto o **TCP** opera na camada de **Transporte** (4ª).

Qual destas camadas ou conceitos você gostaria de explorar com mais detalhes?";
        } 
        // Tópico Algoritmos
        elseif ($topicId == 2 || stripos($message, 'algoritmo') !== false || stripos($message, 'recurs') !== false || stripos($message, 'fila') !== false || stripos($message, 'pilha') !== false) {
            $response = "Olá! Vamos falar de **Algoritmos** e estruturas de dados.

Vi que você está revisando esses conceitos. Um dos pontos mais cobrados é a diferença de estruturas:
- **Pilha (Stack)**: Funciona no modelo **LIFO** (Last In, First Out) — o último a entrar é o primeiro a sair (como uma pilha de pratos).
- **Fila (Queue)**: Funciona no modelo **FIFO** (First In, First Out) — o primeiro a entrar é o primeiro a sair (como uma fila de banco).

Outro conceito essencial é a **Recursividade**, que ocorre quando uma função chama a si mesma. Para evitar loops infinitos, é obrigatório termos um **caso base** (condição de parada).

Quer ver um exemplo prático de algoritmo recursivo em código?";
        }
        // Prog Web
        elseif ($topicId == 4 || stripos($message, 'web') !== false || stripos($message, 'html') !== false || stripos($message, 'css') !== false || stripos($message, 'javascript') !== false || stripos($message, 'dom') !== false) {
            $response = "Olá! Sobre **Programação Web**:

As três tecnologias base do front-end são:
1. **HTML5**: Estrutura e semântica do conteúdo (tags como `<header>`, `<a>`, `<main>`).
2. **CSS3**: Estilização e layout responsivo (recursos como **Flexbox** e **CSS Grid**).
3. **JavaScript (ES6+)**: Comportamento interativo e manipulação do DOM em tempo real.

Por exemplo, para ouvir o clique em um botão e alterar a cor de fundo da página via JS:
```javascript
const botao = document.getElementById('meu-botao');
botao.addEventListener('click', () => {
    document.body.style.backgroundColor = '#5e5ce6';
});
```

Há alguma dessas tecnologias que você deseja aprofundar, como layouts responsivos ou eventos em JavaScript?";
        }
        // Geral
        else {
            $response = "Olá! Sou o **Assistente Estudaí**. Estou aqui para te ajudar a tirar dúvidas sobre os tópicos de estudo e as recomendações geradas pelo sistema.

Você pode me perguntar sobre:
- **Banco de Dados** (Consultas SQL, JOINs, chaves)
- **Algoritmos** (Pilhas, filas, loops, recursão)
- **Redes** (Camadas OSI, TCP/UDP, HTTP)
- **Programação Web** (HTML, CSS Grid, JavaScript moderno)

Como posso ajudar no seu aprendizado hoje?";
        }

        return response()->json(['reply' => $response]);
    }
}

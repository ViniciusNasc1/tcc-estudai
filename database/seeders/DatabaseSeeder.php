<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Topic;
use App\Models\Content;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Alternative;
use App\Models\QuizResult;
use App\Models\TopicPerformance;
use App\Models\Recommendation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criar Usuários
        $estudante = User::create([
            'name' => 'Vinicius Nascimento',
            'email' => 'vinicius@email.com',
            'password' => Hash::make('senha123'),
            'tipo' => 'estudante',
        ]);

        $admin = User::create([
            'name' => 'Administrador Estudaí',
            'email' => 'admin@email.com',
            'password' => Hash::make('senha123'),
            'tipo' => 'administrador',
        ]);

        // 2. Criar Tópicos
        $topicBD = Topic::create([
            'nome' => 'Banco de Dados',
            'descricao' => 'Conceitos de bancos de dados relacionais, modelagem, SQL e normalização.',
        ]);

        $topicAlg = Topic::create([
            'nome' => 'Algoritmos',
            'descricao' => 'Lógica de programação, estruturas de dados básicas e controle de fluxo.',
        ]);

        $topicRedes = Topic::create([
            'nome' => 'Redes',
            'descricao' => 'Modelos de referência, camadas de protocolos e comunicação na internet.',
        ]);

        $topicProgWeb = Topic::create([
            'nome' => 'Prog. Web',
            'descricao' => 'Desenvolvimento front-end e back-end, tecnologias web, HTML, CSS, JS e frameworks.',
        ]);

        // 3. Criar Conteúdos de Estudo
        // Banco de Dados
        $cSQL = Content::create([
            'titulo' => 'Consultas SQL - SELECT, WHERE e JOIN',
            'descricao' => 'Aprenda a realizar consultas em bancos de dados relacionais utilizando os principais comandos do SQL para seleção, filtragem e junção de tabelas.',
            'url' => 'https://example.com/conteudos/sql-queries',
            'topic_id' => $topicBD->id,
        ]);
        Content::create([
            'titulo' => 'Modelagem Relacional e Chaves Estrangeiras',
            'descricao' => 'Entenda os conceitos de tabelas, integridade referencial, chaves primárias e chaves estrangeiras na modelagem de banco de dados.',
            'url' => 'https://example.com/conteudos/modelagem-relacional',
            'topic_id' => $topicBD->id,
        ]);

        // Algoritmos
        Content::create([
            'titulo' => 'Estruturas de Controle de Fluxo (IF, FOR, WHILE)',
            'descricao' => 'Aprenda a controlar a execução de lógica no código usando desvios condicionais e loops de repetição.',
            'url' => 'https://example.com/conteudos/controle-fluxo',
            'topic_id' => $topicAlg->id,
        ]);
        Content::create([
            'titulo' => 'Recursividade e Funções',
            'descricao' => 'Conceito de modularização de código e como criar funções recursivas de forma segura.',
            'url' => 'https://example.com/conteudos/recursividade',
            'topic_id' => $topicAlg->id,
        ]);

        // Redes
        $cOSI = Content::create([
            'titulo' => 'Modelo OSI e TCP/IP',
            'descricao' => 'Estudo detalhado das camadas de rede (Física, Enlace, Rede, Transporte, Sessão, Apresentação, Aplicação) e protocolos fundamentais de comunicação.',
            'url' => 'https://example.com/conteudos/modelo-osi',
            'topic_id' => $topicRedes->id,
        ]);
        Content::create([
            'titulo' => 'Protocolos HTTP e HTTPS',
            'descricao' => 'Como funciona a comunicação cliente-servidor na web, requisições GET/POST e segurança com SSL/TLS.',
            'url' => 'https://example.com/conteudos/http-https',
            'topic_id' => $topicRedes->id,
        ]);

        // Prog. Web
        Content::create([
            'titulo' => 'HTML5 e CSS3 Básico',
            'descricao' => 'Criação de páginas web estruturadas com tags semânticas e estilização responsiva com Flexbox/CSS Grid.',
            'url' => 'https://example.com/conteudos/html5-css3',
            'topic_id' => $topicProgWeb->id,
        ]);
        Content::create([
            'titulo' => 'JavaScript Moderno (ES6+)',
            'descricao' => 'Manipulação do DOM, eventos, arrow functions, promises e controle de escopo (let/const) na web.',
            'url' => 'https://example.com/conteudos/js-es6',
            'topic_id' => $topicProgWeb->id,
        ]);

        // 4. Criar Quizzes e Questões
        // Quiz 1: Banco de Dados
        $quizBD = Quiz::create([
            'titulo' => 'Quiz - Banco de Dados',
            'descricao' => 'Avalie seus conhecimentos em consultas SQL, comandos JOIN e modelagem relacional.',
            'topic_id' => $topicBD->id,
        ]);

        $qBD1 = Question::create([
            'enunciado' => 'Qual comando SQL é utilizado para consultar dados em uma tabela?',
            'quiz_id' => $quizBD->id,
        ]);
        Alternative::create(['descricao' => 'INSERT', 'correta' => false, 'question_id' => $qBD1->id]);
        Alternative::create(['descricao' => 'UPDATE', 'correta' => false, 'question_id' => $qBD1->id]);
        Alternative::create(['descricao' => 'SELECT', 'correta' => true, 'question_id' => $qBD1->id]);
        Alternative::create(['descricao' => 'DELETE', 'correta' => false, 'question_id' => $qBD1->id]);

        $qBD2 = Question::create([
            'enunciado' => 'Qual cláusula SQL é utilizada para filtrar registros em uma consulta?',
            'quiz_id' => $quizBD->id,
        ]);
        Alternative::create(['descricao' => 'ORDER BY', 'correta' => false, 'question_id' => $qBD2->id]);
        Alternative::create(['descricao' => 'GROUP BY', 'correta' => false, 'question_id' => $qBD2->id]);
        Alternative::create(['descricao' => 'WHERE', 'correta' => true, 'question_id' => $qBD2->id]);
        Alternative::create(['descricao' => 'JOIN', 'correta' => false, 'question_id' => $qBD2->id]);

        $qBD3 = Question::create([
            'enunciado' => 'Qual tipo de JOIN retorna apenas os registros que possuem correspondência em ambas as tabelas?',
            'quiz_id' => $quizBD->id,
        ]);
        Alternative::create(['descricao' => 'LEFT JOIN', 'correta' => false, 'question_id' => $qBD3->id]);
        Alternative::create(['descricao' => 'RIGHT JOIN', 'correta' => false, 'question_id' => $qBD3->id]);
        Alternative::create(['descricao' => 'INNER JOIN', 'correta' => true, 'question_id' => $qBD3->id]);
        Alternative::create(['descricao' => 'FULL OUTER JOIN', 'correta' => false, 'question_id' => $qBD3->id]);

        $qBD4 = Question::create([
            'enunciado' => 'O que é uma chave estrangeira (Foreign Key)?',
            'quiz_id' => $quizBD->id,
        ]);
        Alternative::create(['descricao' => 'Uma chave que identifica unicamente uma linha na própria tabela.', 'correta' => false, 'question_id' => $qBD4->id]);
        Alternative::create(['descricao' => 'Uma coluna que estabelece uma relação entre duas tabelas, referenciando a chave primária de outra tabela.', 'correta' => true, 'question_id' => $qBD4->id]);
        Alternative::create(['descricao' => 'Um índice utilizado para acelerar a velocidade das consultas.', 'correta' => false, 'question_id' => $qBD4->id]);
        Alternative::create(['descricao' => 'Um campo que armazena dados criptografados externos.', 'correta' => false, 'question_id' => $qBD4->id]);

        $qBD5 = Question::create([
            'enunciado' => 'Qual comando é utilizado para remover uma tabela inteira e sua estrutura do banco de dados?',
            'quiz_id' => $quizBD->id,
        ]);
        Alternative::create(['descricao' => 'DELETE TABLE', 'correta' => false, 'question_id' => $qBD5->id]);
        Alternative::create(['descricao' => 'DROP TABLE', 'correta' => true, 'question_id' => $qBD5->id]);
        Alternative::create(['descricao' => 'REMOVE TABLE', 'correta' => false, 'question_id' => $qBD5->id]);
        Alternative::create(['descricao' => 'TRUNCATE TABLE', 'correta' => false, 'question_id' => $qBD5->id]);


        // Quiz 2: Algoritmos
        $quizAlg = Quiz::create([
            'titulo' => 'Quiz - Algoritmos',
            'descricao' => 'Teste sua compreensão sobre fluxo lógico, recursividade e estruturas de dados básicas.',
            'topic_id' => $topicAlg->id,
        ]);

        $qA1 = Question::create([
            'enunciado' => 'Qual estrutura de dados funciona no padrão LIFO (Last In, First Out)?',
            'quiz_id' => $quizAlg->id,
        ]);
        Alternative::create(['descricao' => 'Fila (Queue)', 'correta' => false, 'question_id' => $qA1->id]);
        Alternative::create(['descricao' => 'Pilha (Stack)', 'correta' => true, 'question_id' => $qA1->id]);
        Alternative::create(['descricao' => 'Vetor (Array)', 'correta' => false, 'question_id' => $qA1->id]);
        Alternative::create(['descricao' => 'Árvore Binária', 'correta' => false, 'question_id' => $qA1->id]);

        $qA2 = Question::create([
            'enunciado' => 'Qual laço de repetição é mais adequado quando o número de iterações é previamente conhecido?',
            'quiz_id' => $quizAlg->id,
        ]);
        Alternative::create(['descricao' => 'while', 'correta' => false, 'question_id' => $qA2->id]);
        Alternative::create(['descricao' => 'do-while', 'correta' => false, 'question_id' => $qA2->id]);
        Alternative::create(['descricao' => 'for', 'correta' => true, 'question_id' => $qA2->id]);
        Alternative::create(['descricao' => 'foreach', 'correta' => false, 'question_id' => $qA2->id]);

        $qA3 = Question::create([
            'enunciado' => 'O que caracteriza uma função recursiva?',
            'quiz_id' => $quizAlg->id,
        ]);
        Alternative::create(['descricao' => 'Uma função que possui múltiplos parâmetros de entrada.', 'correta' => false, 'question_id' => $qA3->id]);
        Alternative::create(['descricao' => 'Uma função que chama a si mesma durante a execução.', 'correta' => true, 'question_id' => $qA3->id]);
        Alternative::create(['descricao' => 'Uma função que nunca retorna valor algum.', 'correta' => false, 'question_id' => $qA3->id]);
        Alternative::create(['descricao' => 'Uma função declarada dentro de um laço de repetição.', 'correta' => false, 'question_id' => $qA3->id]);

        $qA4 = Question::create([
            'enunciado' => 'Em algoritmos, qual é a principal finalidade de um "caso base" na recursão?',
            'quiz_id' => $quizAlg->id,
        ]);
        Alternative::create(['descricao' => 'Iniciar a primeira chamada da função.', 'correta' => false, 'question_id' => $qA4->id]);
        Alternative::create(['descricao' => 'Definir os tipos de retorno aceitáveis.', 'correta' => false, 'question_id' => $qA4->id]);
        Alternative::create(['descricao' => 'Interromper as chamadas recursivas, evitando um loop infinito.', 'correta' => true, 'question_id' => $qA4->id]);
        Alternative::create(['descricao' => 'Exibir o resultado na tela do usuário.', 'correta' => false, 'question_id' => $qA4->id]);

        $qA5 = Question::create([
            'enunciado' => 'Qual das seguintes opções é uma estrutura condicional de múltipla escolha alternativa ao IF encadeado?',
            'quiz_id' => $quizAlg->id,
        ]);
        Alternative::create(['descricao' => 'switch-case', 'correta' => true, 'question_id' => $qA5->id]);
        Alternative::create(['descricao' => 'for-in', 'correta' => false, 'question_id' => $qA5->id]);
        Alternative::create(['descricao' => 'try-catch', 'correta' => false, 'question_id' => $qA5->id]);
        Alternative::create(['descricao' => 'while-loop', 'correta' => false, 'question_id' => $qA5->id]);


        // Quiz 3: Redes
        $quizRedes = Quiz::create([
            'titulo' => 'Quiz - Redes de Computadores',
            'descricao' => 'Teste seus conhecimentos sobre o modelo OSI, TCP/IP e protocolos da web.',
            'topic_id' => $topicRedes->id,
        ]);

        $qR1 = Question::create([
            'enunciado' => 'Em qual camada do modelo OSI opera o protocolo HTTP?',
            'quiz_id' => $quizRedes->id,
        ]);
        Alternative::create(['descricao' => 'Rede', 'correta' => false, 'question_id' => $qR1->id]);
        Alternative::create(['descricao' => 'Transporte', 'correta' => false, 'question_id' => $qR1->id]);
        Alternative::create(['descricao' => 'Enlace', 'correta' => false, 'question_id' => $qR1->id]);
        Alternative::create(['descricao' => 'Aplicação', 'correta' => true, 'question_id' => $qR1->id]);

        $qR2 = Question::create([
            'enunciado' => 'Qual protocolo da camada de Transporte é orientado à conexão e garante a entrega dos pacotes?',
            'quiz_id' => $quizRedes->id,
        ]);
        Alternative::create(['descricao' => 'UDP', 'correta' => false, 'question_id' => $qR2->id]);
        Alternative::create(['descricao' => 'IP', 'correta' => false, 'question_id' => $qR2->id]);
        Alternative::create(['descricao' => 'TCP', 'correta' => true, 'question_id' => $qR2->id]);
        Alternative::create(['descricao' => 'DNS', 'correta' => false, 'question_id' => $qR2->id]);

        $qR3 = Question::create([
            'enunciado' => 'Qual é a principal diferença entre os protocolos HTTP e HTTPS?',
            'quiz_id' => $quizRedes->id,
        ]);
        Alternative::create(['descricao' => 'O HTTPS é mais rápido porque não exige conexão de rede.', 'correta' => false, 'question_id' => $qR3->id]);
        Alternative::create(['descricao' => 'O HTTPS criptografa os dados transmitidos usando SSL/TLS, garantindo segurança.', 'correta' => true, 'question_id' => $qR3->id]);
        Alternative::create(['descricao' => 'O HTTP é utilizado apenas em dispositivos móveis.', 'correta' => false, 'question_id' => $qR3->id]);
        Alternative::create(['descricao' => 'Não existe diferença prática, são apenas nomenclaturas diferentes.', 'correta' => false, 'question_id' => $qR3->id]);

        $qR4 = Question::create([
            'enunciado' => 'Qual porta padrão é utilizada pelo serviço web seguro HTTPS?',
            'quiz_id' => $quizRedes->id,
        ]);
        Alternative::create(['descricao' => '80', 'correta' => false, 'question_id' => $qR4->id]);
        Alternative::create(['descricao' => '21', 'correta' => false, 'question_id' => $qR4->id]);
        Alternative::create(['descricao' => '443', 'correta' => true, 'question_id' => $qR4->id]);
        Alternative::create(['descricao' => '8080', 'correta' => false, 'question_id' => $qR4->id]);

        $qR5 = Question::create([
            'enunciado' => 'Qual dispositivo de rede é responsável por encaminhar pacotes entre diferentes redes de computadores?',
            'quiz_id' => $quizRedes->id,
        ]);
        Alternative::create(['descricao' => 'Switch', 'correta' => false, 'question_id' => $qR5->id]);
        Alternative::create(['descricao' => 'Roteador', 'correta' => true, 'question_id' => $qR5->id]);
        Alternative::create(['descricao' => 'Hub', 'correta' => false, 'question_id' => $qR5->id]);
        Alternative::create(['descricao' => 'Repetidor', 'correta' => false, 'question_id' => $qR5->id]);


        // Quiz 4: Prog. Web
        $quizPW = Quiz::create([
            'titulo' => 'Quiz - Programação Web',
            'descricao' => 'Avalie seus conhecimentos em linguagens básicas do front-end: HTML, CSS e JavaScript.',
            'topic_id' => $topicProgWeb->id,
        ]);

        $qW1 = Question::create([
            'enunciado' => 'Qual tag HTML é utilizada para criar um link para outra página?',
            'quiz_id' => $quizPW->id,
        ]);
        Alternative::create(['descricao' => '<link>', 'correta' => false, 'question_id' => $qW1->id]);
        Alternative::create(['descricao' => '<a>', 'correta' => true, 'question_id' => $qW1->id]);
        Alternative::create(['descricao' => '<href>', 'correta' => false, 'question_id' => $qW1->id]);
        Alternative::create(['descricao' => '<url>', 'correta' => false, 'question_id' => $qW1->id]);

        $qW2 = Question::create([
            'enunciado' => 'Em CSS, qual propriedade é utilizada para alterar a cor do texto de um elemento?',
            'quiz_id' => $quizPW->id,
        ]);
        Alternative::create(['descricao' => 'background-color', 'correta' => false, 'question_id' => $qW2->id]);
        Alternative::create(['descricao' => 'font-color', 'correta' => false, 'question_id' => $qW2->id]);
        Alternative::create(['descricao' => 'color', 'correta' => true, 'question_id' => $qW2->id]);
        Alternative::create(['descricao' => 'text-style', 'correta' => false, 'question_id' => $qW2->id]);

        $qW3 = Question::create([
            'enunciado' => 'No JavaScript moderno, qual palavra-chave permite declarar uma variável cujo valor não pode ser reatribuído?',
            'quiz_id' => $quizPW->id,
        ]);
        Alternative::create(['descricao' => 'var', 'correta' => false, 'question_id' => $qW3->id]);
        Alternative::create(['descricao' => 'let', 'correta' => false, 'question_id' => $qW3->id]);
        Alternative::create(['descricao' => 'const', 'correta' => true, 'question_id' => $qW3->id]);
        Alternative::create(['descricao' => 'static', 'correta' => false, 'question_id' => $qW3->id]);

        $qW4 = Question::create([
            'enunciado' => 'Qual método JavaScript é utilizado para selecionar um elemento do DOM através do seu atributo ID?',
            'quiz_id' => $quizPW->id,
        ]);
        Alternative::create(['descricao' => 'document.selectElement()', 'correta' => false, 'question_id' => $qW4->id]);
        Alternative::create(['descricao' => 'document.getElementById()', 'correta' => true, 'question_id' => $qW4->id]);
        Alternative::create(['descricao' => 'document.findId()', 'correta' => false, 'question_id' => $qW4->id]);
        Alternative::create(['descricao' => 'document.querySelectorID()', 'correta' => false, 'question_id' => $qW4->id]);

        $qW5 = Question::create([
            'enunciado' => 'Qual das opções abaixo descreve corretamente a função do CSS Grid?',
            'quiz_id' => $quizPW->id,
        ]);
        Alternative::create(['descricao' => 'Gerenciar conexões de banco de dados no front-end.', 'correta' => false, 'question_id' => $qW5->id]);
        Alternative::create(['descricao' => 'Facilitar a criação de layouts bidimensionais flexíveis e alinhados em linhas e colunas.', 'correta' => true, 'question_id' => $qW5->id]);
        Alternative::create(['descricao' => 'Criar animações 3D complexas no navegador.', 'correta' => false, 'question_id' => $qW5->id]);
        Alternative::create(['descricao' => 'Estilizar exclusivamente fontes tipográficas.', 'correta' => false, 'question_id' => $qW5->id]);


        // 5. Criar Histórico de Atividades e Resultados do Estudante (Mock Estado Inicial)
        // Resultados de Quizzes
        QuizResult::create([
            'user_id' => $estudante->id,
            'quiz_id' => $quizBD->id,
            'pontuacao_percentual' => 45.0, // 45% (Abaixo de 50%) -> Prioritário
            'total_questoes' => 20,
            'total_acertos' => 9,
            'created_at' => now()->subDays(5),
        ]);

        QuizResult::create([
            'user_id' => $estudante->id,
            'quiz_id' => $quizAlg->id,
            'pontuacao_percentual' => 85.0, // 85% (Acima de 80%) -> Dominado
            'total_questoes' => 20,
            'total_acertos' => 17,
            'created_at' => now()->subDays(2),
        ]);

        // Desempenho por Tópico inicial
        TopicPerformance::create([
            'user_id' => $estudante->id,
            'topic_id' => $topicBD->id,
            'percentual_acertos' => 45.0,
            'classificacao' => 'estudo_prioritario',
        ]);

        TopicPerformance::create([
            'user_id' => $estudante->id,
            'topic_id' => $topicAlg->id,
            'percentual_acertos' => 85.0,
            'classificacao' => 'dominado',
        ]);

        TopicPerformance::create([
            'user_id' => $estudante->id,
            'topic_id' => $topicRedes->id,
            'percentual_acertos' => 70.0, // mockado sem ter feito o quiz ainda (ex: simulado)
            'classificacao' => 'revisao_leve',
        ]);

        TopicPerformance::create([
            'user_id' => $estudante->id,
            'topic_id' => $topicProgWeb->id,
            'percentual_acertos' => 20.0,
            'classificacao' => 'estudo_prioritario',
        ]);

        // Recomendações iniciais
        Recommendation::create([
            'user_id' => $estudante->id,
            'content_id' => $cSQL->id,
            'topic_id' => $topicBD->id,
            'motivo' => 'Você acertou apenas 45% das questões de Banco de Dados. A IA identificou dificuldades específicas em consultas SQL (SELECT, WHERE, JOIN).',
            'status' => 'pendente',
            'created_at' => now()->subDays(5),
        ]);

        Recommendation::create([
            'user_id' => $estudante->id,
            'content_id' => $cOSI->id,
            'topic_id' => $topicRedes->id,
            'motivo' => 'Seu desempenho em Redes foi de 70%. Revisar o Modelo OSI e suas camadas pode ajudar a consolidar seu aprendizado.',
            'status' => 'pendente',
            'created_at' => now()->subDays(1),
        ]);
    }
}

# Sistema de Gerenciamento de PPC (API Backend)

Este projeto consiste no desenvolvimento do backend para um sistema protótipo mediador de envio, avaliação e aprovação de Projetos Pedagógicos de Curso (PPC) por parte de uma Unidade Acadêmica. O fluxo da aplicação gira em torno de uma Proposta de curso que pode ser criada por uma Unidade Acadêmica, a qual é enviada para análise/correção de um Avaliador responsável, que pode retornar a Proposta para a Unidade Acadêmica com uma observação ou permitir que a Proposta avance para ser aprovada ou reprovada pela Câmara de Ensino.

---

## 🛠️ Requisitos Mínimos

* PHP >= 8.2
* Composer
* MySQL 

---

## 🚀 Como Executar o Projeto Localmente

Siga os passos abaixo para clonar, configurar e rodar a API em seu ambiente de desenvolvimento:

### 1. Clone o repositório
```bash
git clone https://github.com/josuearmando/desafio-ppc.git
cd desafio-ppc
```

### 2. Instalar as Dependências do PHP
```bash
composer install
```

### 3. Configurar o Arquivo de Ambiente
Copie o arquivo de exemplo `.env.example` para `.env`:
```bash
cp .env.example .env
```

Agora, abra o arquivo `.env` gerado e configure as credenciais do seu banco de dados MySQL local:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 4. Gerar a Chave da Aplicação Laravel
```bash
php artisan key:generate
```

### 5. Executar as Migrations e os Seeders
Para criar a estrutura de tabelas no banco de dados e populá-lo automaticamente com os dados iniciais de teste (Unidades Acadêmicas, Avaliadores, etc.), execute:
```bash
php artisan migrate --seed
```

### 6. Iniciar o Servidor Embutido
```bash
php artisan serve
```
A API estará ativa e pronta para receber requisições em: `http://127.0.0.1:8000`

---

## 🧪 Testes das Rotas via cURL

Você pode testar os endpoints da API utilizando os comandos `cURL` abaixo diretamente no seu terminal (certifique-se de que o comando `php artisan serve` está rodando em outra aba).

### 1. Listar todas as Propostas de PPC
Retorna a lista de projetos pedagógicos cadastrados no sistema.
```bash
curl -X GET http://127.0.0.1:8000/api/propostas      -H "Accept: application/json"
```

### 2. Submeter um Novo PPC (Unidade Acadêmica)
Simula o envio de um novo projeto de curso contendo uma grade curricular com disciplinas dinâmicas.
```bash
curl -X POST http://127.0.0.1:8000/api/propostas \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{
       "unidade_academica_id": 1,
       "nome": "Bacharelado em Sistemas de Informação",
       "carga_horaria_total": 3200,
       "quantidade_semestres": 8,
       "justificativa": "Demanda crescente por profissionais de tecnologia na região Norte.",
       "impacto_social": "Inserção de jovens no mercado de trabalho digital.",
       "disciplinas": [
         {
           "nome": "Algoritmos e Estruturas de Dados",
           "carga_horaria": 80,
           "semestre": 1
         },
         {
           "nome": "Banco de Dados I",
           "carga_horaria": 60,
           "semestre": 3
         }
       ]
     }'
```

### 3. Detalhar e Reservar Proposta (Avaliador Técnico)
Busca os detalhes de uma proposta específica pelo ID e realiza o fluxo de reserva para o avaliador.
```bash
curl -X GET http://127.0.0.1:8000/api/propostas/1      -H "Accept: application/json"
```
*(Substitua o número `1` ao final da URL pelo ID de uma proposta válida no seu banco)*

### 4. Registrar Parecer Técnico (Aprovar/Reprovar com Observação)
Envia o veredito do avaliador técnico sobre o projeto analisado para que ele possa seguir para a Câmara de Graduação.
```bash
curl -X POST http://127.0.0.1:8000/api/avaliacoes      -H "Content-Type: application/json"      -H "Accept: application/json"      -d '{
       "proposta_id": 1,
       "avaliador_id": 1,
       "status_resultado": "enviado_camara",
       "observacoes": "A matriz curricular atende perfeitamente às diretrizes nacionais do Ministério da Educação e da UFPA."
     }'
```

### 5. Julgamento Final de Pauta (Câmara de Graduação)
Registra a decisão final tomada pela banca julgadora da Câmara de Graduação sobre a proposta de curso.
```bash
curl -X POST http://127.0.0.1:8000/api/camara/propostas/1/decisao      -H "Content-Type: application/json"      -H "Accept: application/json"      -d '{
       "status": "aprovado"
     }'
```
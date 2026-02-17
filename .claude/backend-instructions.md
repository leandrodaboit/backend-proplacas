# Backend Instructions

## Contexto Geral

Este projeto é uma API desenvolvida em **Laravel 12**.
A aplicação é voltada para integração com frontend web, desktop e mobile.
Por padrão, todas as implementações devem considerar que se trata de uma API.
Caso exista alguma parte visual específica, isso será explicitamente informado.

O foco principal deve ser:

- Código limpo
- Alta manutenibilidade
- Reaproveitamento
- Baixo acoplamento
- Facilidade de evolução

Sempre priorizar decisões que tornem o sistema sustentável a longo prazo.

---

## Stack Principal

- PHP 8.3.30
- Laravel 12
- Laravel Sanctum 4 para autenticação
- Spatie Laravel Permission para controle de permissões
- Spatie Laravel Data para DTOs
- Pest 4 para testes automatizados
- Laravel Pint para formatação de código
- Laravel Sail para ambiente de desenvolvimento

---

## Arquitetura e Responsabilidades

### Controller

- Responsável exclusivamente pela camada HTTP
- Recebe Requests
- Chama Services
- Retorna Resources
- Não deve conter regra de negócio
- Não deve conter queries complexas

---

### Request

- Utiliza o padrão nativo do Laravel
- Responsável por validação de entrada
- Utilizado nos Controllers

---

### Service

- Responsável por toda regra de negócio
- Deve receber DTOs
- Pode utilizar Repositories
- Pode lançar Exceptions personalizadas
- Não deve depender de Request
- Deve ser reutilizável

---

### Repository

- Responsável exclusivamente por queries
- Encapsula acesso ao Model
- Não deve conter regra de negócio
- Deve facilitar reaproveitamento de consultas

---

### Model

- Representa as entidades do sistema
- Pode utilizar Traits
- Pode conter casts, scopes e relacionamentos
- Evitar colocar regra de negócio complexa no Model

---

### DTO

- Implementado utilizando Spatie Laravel Data
- Utilizado principalmente nos Services
- Serve como objeto de transporte entre camadas
- Deve manter tipagem clara
- Deve facilitar validação e transformação de dados

---

### Resource

- Utilizado como padrão de retorno no Controller
- Responsável por formatar resposta da API
- Não deve conter regra de negócio

---

### Middleware

- Utilizado quando necessário
- Pode controlar autenticação, autorização ou regras transversais

---

### Enums

- Utilizar sempre que houver necessidade de representar estados fixos
- Evitar uso de strings soltas no código
- Melhorar legibilidade e segurança

---

### Exceptions Personalizadas

- Utilizar quando necessário
- Devem representar erros de domínio
- Não expor detalhes internos desnecessários
- Melhorar clareza e controle de fluxo

---

### Traits

- Podem ser utilizadas para reutilização de comportamento
- Evitar uso excessivo
- Usar apenas quando fizer sentido estruturalmente

---

### Interfaces

- Utilizar quando houver necessidade de abstração
- Principalmente para Services e Repositories
- Facilitar testes e substituição de implementações

---

### Form Requests

- Utilizar para validação em Controllers
- Centralizar regras de validação
- Incluir mensagens de erro customizadas
- Nunca colocar validação inline no Controller

---

### Factories e Seeders

- Criar factories ao criar modelos (padrão do Artisan)
- Usar factories para gerar dados de teste
- Seeders para popular dados iniciais no banco

---

## Laravel Sail

- **Todos** os comandos PHP, Artisan, Composer e Node devem rodar via `vendor/bin/sail`
- Exemplos:
  - `vendor/bin/sail artisan migrate`
  - `vendor/bin/sail composer install`
  - `vendor/bin/sail npm run dev`
  - `vendor/bin/sail php script.php`
- Iniciar serviços: `vendor/bin/sail up -d`
- Parar serviços: `vendor/bin/sail stop`

---

## Formatação de Código

- Usar **Laravel Pint** para manter consistência
- Executar antes de finalizar qualquer código: `vendor/bin/sail bin pint --dirty`
- Isso garante que o código segue o padrão do projeto

---

## Laravel 12 Específico

- Middleware são registrados em `bootstrap/app.php`, não em `app/Http/Kernel.php`
- Console commands em `app/Console/Commands/` são auto-descobertos
- Usar `bootstrap/app.php` para configurar middleware, exceções e rotas
- Usar `bootstrap/providers.php` para service providers específicos da aplicação

---

## Variáveis de Ambiente

- **Nunca** usar `env()` fora de arquivos de configuração
- Sempre usar `config('app.name')` ao invés de `env('APP_NAME')`
- Configurações devem estar em `config/`

---

## Boas Práticas com Banco de Dados

### Eager Loading (N+1 Prevention)

- Usar `with()` para evitar N+1 queries
- Exemplo: `User::with('posts')->get()` ao invés de carregar posts depois
- Sempre revisar queries para garantir eficiência

### Eloquent vs Raw Queries

- Preferir Eloquent e Query Builder
- Evitar `DB::` quando possível
- Usar `Model::query()` ao invés de construir queries manualmente

### Relacionamentos

- Sempre usar type hints em métodos de relacionamento
- Manter relacionamentos declarados no Model

---

## Otimizações

### Queued Jobs

- Usar `ShouldQueue` para operações demoradas
- Não bloquear requisições HTTP com operações pesadas
- Exemplo: envio de emails, processamento de arquivos

### Named Routes

- Usar `route('nome-da-rota')` ao gerar URLs
- Nunca hardcodar URLs na aplicação

---

## Autenticação e Permissões

- Autenticação feita via Laravel Sanctum
- Controle de permissões utilizando Spatie Permission
- Sempre validar permissões quando necessário
- Evitar lógica de autorização espalhada

---

## Testes Automatizados com Pest 4

- O projeto utiliza **Pest 4** para testes automatizados
- **Testes são obrigatórios** para qualquer nova funcionalidade
- Criar testes: `vendor/bin/sail artisan make:test --pest NomeTeste`
- Criar testes unitários: `vendor/bin/sail artisan make:test --pest --unit NomeTeste`
- Rodar testes: `vendor/bin/sail artisan test --compact`
- Rodar teste específico: `vendor/bin/sail artisan test --compact --filter=nomeDo Teste`

### Prioridades de Testes

- Services (regras de negócio)
- Controllers (fluxo HTTP e respostas)
- Regras críticas do domínio
- Integração com banco de dados

### Diretrizes para Testes

- Testes devem ser claros e legíveis
- Evitar testes excessivamente acoplados à implementação
- Utilizar factories para criação de dados
- Isolar dependências quando necessário
- Garantir que regras de negócio estejam bem cobertas
- Usar `search-docs` para documentação específica do Pest 4
- **Não deletar testes** sem aprovação

### Objetivo dos Testes

- Garantir estabilidade do sistema
- Facilitar refatorações
- Evitar regressões
- Aumentar confiança na evolução do código
- Documentar comportamento esperado

---

## Boas Práticas Obrigatórias

- Sempre priorizar manutenibilidade
- Código deve ser claro e reutilizável
- Evitar duplicação
- Aplicar princípios SOLID quando fizer sentido
- Separação clara de responsabilidades
- Evitar lógica no Controller
- Evitar acoplamento desnecessário
- Escrever código pensando em evolução futura
- Garantir que a API seja consistente e previsível

---

## Padrão Geral de Fluxo

Request → Controller → Service → Repository → Model  
Service retorna dados → Controller retorna Resource

---

## Diretriz Final

Toda implementação deve considerar:

- Facilidade de manutenção
- Clareza de responsabilidades
- Reaproveitamento
- Escalabilidade
- Padronização

Sempre optar pela solução mais limpa e sustentável.

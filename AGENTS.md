# AGENT – Laravel API Architecture & Engineering Guide

Você é um agente especialista em desenvolvimento de APIs com Laravel moderno.
Atue sempre como um engenheiro de software sênior, tomando decisões técnicas conscientes, justificáveis e alinhadas com boas práticas de mercado.

## Objetivo do Projeto
Este projeto é uma API construída com Laravel, focada em:
- Escalabilidade
- Manutenibilidade
- Clareza arquitetural
- Separação correta de responsabilidades
- Código limpo, testável e evolutivo

Nunca implemente soluções rápidas ou acopladas. Priorize sempre qualidade técnica.

---

## Arquitetura Obrigatória

Utilize arquitetura em camadas, respeitando rigorosamente as responsabilidades.

### Controller
- Deve ser fino e simples
- Responsável apenas por:
    - Receber a requisição
    - Validar dados via Form Request
    - Delegar a lógica para o Service
    - Retornar respostas HTTP padronizadas
- Nunca conter regras de negócio

### Service
- Camada central de regras de negócio
- Orquestra fluxos, validações complexas e decisões
- Pode chamar múltiplos repositórios
- Não acessa diretamente Request ou Response
- Deve ser testável isoladamente

### Repository
- Responsável exclusivamente pelo acesso a dados
- Encapsula queries Eloquent ou Query Builder
- Nunca conter regra de negócio
- Sempre utilizar Interfaces

### Model
- Representa apenas a entidade e seus relacionamentos
- Usar casts, scopes e atributos computados quando fizer sentido
- Evitar lógica de negócio pesada dentro do Model

---

## Interfaces e Injeção de Dependência

- Todo Repository deve ter uma Interface
- Services devem depender de Interfaces, nunca de implementações
- Registrar bindings no Service Provider
- Seguir o princípio de inversão de dependência (SOLID)

Exemplo de binding:

$this->app->bind(
UserRepositoryInterface::class,
UserRepository::class
);

---

## Observers e Events

Utilize Observers quando:
- Reagir a eventos do ciclo de vida do Model
- Registrar logs
- Disparar eventos
- Manter histórico
- Evitar lógica duplicada

Utilize Events e Listeners quando:
- A ação gerar efeitos colaterais
- Houver necessidade de desacoplamento
- A lógica puder ser assíncrona no futuro

Nunca sobrecarregue Controllers ou Services com esse tipo de responsabilidade.

---

## Validação

- Toda validação de entrada deve ser feita via Form Request
- Nunca validar diretamente no Controller
- Mensagens devem ser claras e padronizadas
- Regras complexas devem ser extraídas para Rules customizadas

---

## Padrões de Código

- Seguir PSR-12
- Métodos pequenos e com responsabilidade única
- Nomes claros e autoexplicativos
- Evitar comentários óbvios
- Priorizar legibilidade ao invés de complexidade

---

## Respostas da API

- Utilizar JSON como padrão
- Padronizar estrutura de resposta
- Nunca retornar exceções cruas
- Utilizar API Resources quando necessário

Exemplo de resposta padrão:

{
"success": true,
"data": {},
"message": "Operação realizada com sucesso"
}

---

## Tratamento de Erros

- Utilizar Exceptions customizadas
- Centralizar tratamento no Handler
- Nunca tratar erros diretamente no Controller
- Mensagens amigáveis para o consumidor da API

---

## Testes

- Priorizar testes de Service e Repository
- Controllers devem ser testados via Feature Tests
- Nunca escrever código sem pensar em testabilidade

---

## Decisões Técnicas

Antes de implementar qualquer funcionalidade, avalie:
- Respeita a separação de responsabilidades?
- É testável?
- Escala corretamente?
- Evita acoplamento?
- Segue boas práticas do Laravel?

Se qualquer resposta for negativa, refaça a abordagem.

---

## Regra Final

Sempre escolha a solução mais simples possível, mas nunca mais simples do que o necessário.
Qualidade, clareza e arquitetura vêm antes de velocidade.

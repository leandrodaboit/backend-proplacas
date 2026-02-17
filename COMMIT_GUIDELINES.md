# Padrão de Commits

## Objetivo

Manter um histórico de commits claro, consistente e facilmente legível para toda a equipe de desenvolvimento.

## Formato Geral

```
<tipo>(<escopo>): <assunto>

<corpo>

<rodapé>
```

## Componentes

### 1. Tipo (Obrigatório)

Define a natureza da mudança:

| Tipo | Descrição | Exemplo |
|------|-----------|---------|
| `feat` | Nova funcionalidade | `feat(auth): adicionar autenticação com Sanctum` |
| `fix` | Correção de bug | `fix(user): corrigir validação de email` |
| `docs` | Documentação | `docs: atualizar README com instruções` |
| `style` | Formatação, sem lógica | `style: formatar código com Pint` |
| `refactor` | Refatoração, sem mudança de comportamento | `refactor: reorganizar estrutura de pastas` |
| `test` | Adicionar ou atualizar testes | `test: adicionar testes para UserService` |
| `chore` | Tarefas de manutenção | `chore: atualizar dependências` |
| `perf` | Melhoria de performance | `perf: otimizar queries com eager loading` |
| `ci` | Mudanças em CI/CD | `ci: configurar GitHub Actions` |

### 2. Escopo (Opcional)

Define o contexto/módulo afetado:

```
feat(auth): implementação de autenticação
feat(api): criar novo endpoint de usuários
fix(validation): corrigir regra de email
```

### 3. Assunto (Obrigatório)

Breve descrição do que foi feito:

- ✅ **Máximo 100 caracteres** (incluindo tipo e escopo)
- ✅ Início com verbo no imperativo (adicionar, criar, corrigir, etc)
- ✅ Sem ponto final
- ✅ Em português brasileiro
- ❌ Não começar com letra maiúscula após os dois-pontos
- ❌ Não usar abreviações ou siglas desnecessárias

### 4. Corpo (Opcional, mas recomendado)

Explicação detalhada das mudanças:

- Separar do assunto por uma linha em branco
- Explicar **o quê** e **por quê**, não o **como**
- Quebrar linhas com 72 caracteres
- Pode ter múltiplos parágrafos

```
Este commit implementa a validação de email em dois cenários:
- Durante o registro de novo usuário
- Durante a atualização de perfil

A validação utiliza regex padrão de RFC 5322 e verifica
se o email já existe no banco de dados.
```

### 5. Rodapé (Opcional)

Referências a issues, breaking changes, etc:

```
Fecha #123
Relacionado a #456
BREAKING CHANGE: endpoint /users/:id foi removido
```

## Exemplos de Commits

### ✅ Bons Exemplos

```
feat(models): criar modelo de Plano com relacionamentos
```

```
fix(auth): corrigir bug de token expirado prematuramente

O token estava sendo marcado como expirado com 5 minutos
de antecedência devido a diferença de timezone.

Fecha #42
```

```
docs: adicionar instruções de setup com Sail
```

```
refactor: reorganizar estrutura do projeto em features

Mudanças:
- Pasta 'app/Services' reorganizada em 'app/Features'
- Cada feature agora tem seus próprios services e models
- Melhora a escalabilidade e manutenibilidade

Relacionado a #78
```

```
test: adicionar cobertura para UserService

- teste para criação de usuário
- teste para atualização de perfil
- teste para remoção de conta
```

### ❌ Maus Exemplos

```
corrigir bug                              # Muito vago, sem tipo
```

```
feat: Adicionar nova funcionalidade       # Maiúscula após dois-pontos
```

```
feat: adicionar validação de email muito importante para o sistema e que garante que o usuário não consiga registrar com email inválido
                                          # Excede 100 caracteres
```

```
fix(auth): arrumei o bug do login        # Linguagem casual
```

## Regras Obrigatórias

### Tamanho do Assunto
- **Mínimo:** 5 caracteres
- **Máximo:** 100 caracteres (incluindo tipo e escopo)

### Linguagem
- **Sempre em português brasileiro**
- Sem abreviações (crie, não "cria"; adicione, não "add")
- Sem gírias ou linguagem casual

### Verbo no Imperativo
Use o imperativo (como se fosse uma ordem):

```
✅ adicionar     ❌ adicionada / adicionei / adicionando
✅ corrigir      ❌ corrigida / corrigi / corrigindo
✅ atualizar     ❌ atualizada / atualizei / atualizando
✅ remover       ❌ removida / removi / removendo
✅ criar         ❌ criada / criei / criando
```

### Quando Não Usar Escopo
- Em commits de documentação geral
- Em commits de configuração global
- Em commits que afetam múltiplos módulos

```
✅ docs: atualizar README
✅ chore: atualizar dependências
❌ docs(auth): atualizar README
```

## Co-Autoria

Para commits colaborativos, use rodapé:

```
refactor: reorganizar estrutura de pastas

Co-Authored-By: João Silva <joao@email.com>
Co-Authored-By: Maria Santos <maria@email.com>
```

## Commits Automáticos (Claude Code)

Commits gerados por Claude Code devem incluir:

```
<tipo>: <mensagem em português>

Co-Authored-By: Claude Haiku 4.5 <noreply@anthropic.com>
```

## Convenção de Breaking Changes

Adicione `!` antes do dois-pontos para indicar breaking change:

```
feat!: remover endpoint /api/users/:id

BREAKING CHANGE: endpoint /api/users/:id foi removido.
Use /api/users/delete/:id ao invés disso.
```

## Checklist para Cada Commit

Antes de fazer um commit, verifique:

- [ ] Tipo está correto (feat, fix, docs, etc)
- [ ] Assunto tem máximo 100 caracteres
- [ ] Assunto começa com verbo no imperativo
- [ ] Assunto está em português brasileiro
- [ ] Sem ponto final no assunto
- [ ] Corpo explica o **quê** e **por quê**
- [ ] Corpo respeita 72 caracteres por linha
- [ ] Rodapé inclui referências a issues se aplicável
- [ ] Testes passam (se aplicável)
- [ ] Código foi formatado com Pint

## Ferramentas Recomendadas

### Git Hooks (Opcional)
Para forçar o padrão, pode usar `husky` com `commitlint`:

```bash
npm install --save-dev husky @commitlint/cli @commitlint/config-conventional
```

### Aliases Git (Opcional)
Adicione ao `.gitconfig`:

```bash
git config --global commit.template ~/.gitmessage
```

## Referências

- [Conventional Commits](https://www.conventionalcommits.org/)
- [How to Write a Git Commit Message - Chris Beams](https://chris.beams.io/posts/git-commit/)
- [Commit Message Conventions](https://gist.github.com/stephenparish/9941e89d80e2bc58612f)

## Perguntas Frequentes

**P: E se for um commit muito grande?**
A: Divida em múltiplos commits. Cada commit deve representar uma mudança lógica coerente.

**P: E se for uma correção de outro commit?**
A: Use `git commit --amend` ao invés de criar novo commit.

**P: E se esqueci do padrão?**
A: Use `git reset HEAD~1` e refaça com o padrão correto.

**P: Posso usar emojis?**
A: Não recomendado. Mantenha legibilidade e paridade em diferentes ferramentas.

**P: E commits de merge?**
A: Evite. Use `git rebase` ao invés de merge quando possível para manter histórico limpo.

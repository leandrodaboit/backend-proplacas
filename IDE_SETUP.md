# Configuração de IDE - PHPStorm

## IDE Recomendada

**PHPStorm** (JetBrains) é a IDE recomendada para este projeto.

- ✅ Suporte completo a Laravel
- ✅ Integração com Docker (Laravel Sail)
- ✅ Debugging nativo
- ✅ Ferramentas de refatoração avançadas
- ✅ Análise de código estática (Pint, Pest)

## Por que PHPStorm?

Este projeto é configurado para **PHPStorm** porque:

1. **Suporte Nativo a Laravel**: Plugins e configurações otimizadas
2. **Docker Integration**: Funciona perfeitamente com Laravel Sail
3. **Debugging**: XDebug configurado e pronto para usar
4. **Refatoração**: Ferramentas avançadas de refatoração segura
5. **Performance**: Otimizado para projetos PHP grandes

## Instalação

### 1. Download
```bash
# Linux / Windows / macOS
https://www.jetbrains.com/phpstorm/download/
```

### 2. Primeira Execução
```bash
# Abrir o projeto
open -a "PhpStorm" .
```

### 3. Configuração Inicial
PHPStorm configurará automaticamente ao detectar:
- `composer.json`
- `docker-compose.yml` (Sail)
- `.idea/`

## Configuração com Laravel Sail

### Interpreter PHP
```
Preferences > PHP > CLI Interpreters
  Nome: Docker (Sail)
  Docker Compose: docker-compose.yml
  Service: laravel.test
```

### Run/Debug Configuration
```
Run > Edit Configurations
  Add PHP Built-in Web Server
  Document Root: /app/public
  Start browser: http://localhost:8000
```

### Debugging (XDebug)
```
Preferences > PHP > Debug
  Debug Port: 9003
  Ignore External Connections: unchecked
```

## Atalhos Úteis (macOS)

| Atalho | Ação |
|--------|------|
| `⌘ + Shift + O` | Abrir arquivo por nome |
| `⌘ + Shift + A` | Buscar ação |
| `⌘ + /` | Comentar/Descomentar linha |
| `⌥ + Enter` | Mostrar intenções (quick fix) |
| `⌘ + K` | Commit |
| `⌘ + Shift + K` | Push |
| `⌘ + D` | Debugar |

## Atalhos Úteis (Linux/Windows)

| Atalho | Ação |
|--------|------|
| `Ctrl + Shift + O` | Abrir arquivo por nome |
| `Ctrl + Shift + A` | Buscar ação |
| `Ctrl + /` | Comentar/Descomentar linha |
| `Alt + Enter` | Mostrar intenções (quick fix) |
| `Ctrl + K` | Commit |
| `Ctrl + Shift + K` | Push |
| `Ctrl + D` | Debugar |

## Extensões Recomendadas (Plugins)

### Pré-instalados
- ✅ Laravel Plugin
- ✅ PHP Built-in Support
- ✅ Git Integration
- ✅ Docker Support

### Adicionais Recomendados
```
Preferências > Plugins > Marketplace

- Laravel Idea (Opcional, mas poderoso)
- Pest Support (Para testes Pest)
- Database Navigator (Para queries)
```

## Padrão de Código (Code Style)

PHPStorm detectará automaticamente:
- ✅ PSR-12 via Pint
- ✅ Indentação (4 espaços)
- ✅ Quebra de linhas

Para verificar/aplicar:
```bash
# Dentro do PHPStorm
Code > Reformat Code (Ctrl+Alt+L / Cmd+Alt+L)
```

Ou via linha de comando:
```bash
vendor/bin/sail bin pint
```

## Debugging (Passo a Passo)

### 1. Adicionar Breakpoint
```
Clique no número da linha para adicionar um ponto de parada
```

### 2. Iniciar Debugger
```
Run > Debug 'app'
ou Cmd+D / Ctrl+D
```

### 3. Controlar Execução
```
Step Over: F10
Step Into: F11
Step Out: Shift+F11
Resume Program: Cmd+Option+R / Ctrl+F8
```

## Ferramentas Integradas

### Terminal
```
View > Tool Windows > Terminal
ou Alt+F12 / Ctrl+Alt+T
```

### VCS (Git)
```
View > Tool Windows > Git
Commit/Push com Cmd+K / Ctrl+K
```

### Database
```
View > Tool Windows > Database
Conectar com banco Docker via Sail
```

## Configurações Importantes

### .idea/ na Raiz
```
✅ Mantido no .gitignore (local)
❌ Não é commitado ao repositório
✅ Cada desenvolvedor tem sua própria configuração
```

### Estrutura de Pastas
```
✅ Configurado Path Aliases
  config/ → Configurações Laravel
  app/    → Código da aplicação
  tests/  → Testes Pest
  routes/ → Definições de rotas
```

## Troubleshooting

### PHPStorm não reconhece Docker
```
1. Preferences > Docker
2. Conectar ao Docker Desktop/socket
3. Recarregar projeto (File > Reload)
```

### Debugging não funciona
```
1. Verificar XDebug no container
2. vendor/bin/sail tinker
3. phpinfo() deve mostrar XDebug habilitado
```

### Autocompletar não funciona
```
1. File > Invalidate Caches and Restart
2. Marcar /vendor como 'Excluded' se necessário
3. Composer > Reload
```

## Configuração de Equipe

### Compartilhando Configurações
```
# Estas NÃO devem ser commitadas:
.idea/workspace.xml  (local)
.idea/usage.statistics.xml

# Estas PODEM ser compartilhadas (opcional):
.idea/runConfigurations/
.idea/codeStyles/
```

### .idea a Ser Ignorado
```
.gitignore já contém:
/.idea/

Isso garante que cada desenvolvedor
tenha sua própria configuração
```

## Recursos Adicionais

### Documentação Oficial
- [PHPStorm Documentation](https://www.jetbrains.com/help/phpstorm/)
- [Laravel Support](https://www.jetbrains.com/help/phpstorm/laravel.html)
- [Docker Integration](https://www.jetbrains.com/help/phpstorm/docker.html)

### Tutoriais JetBrains
- [PHPStorm for Laravel](https://www.jetbrains.com/help/phpstorm/run-laravel-specific-tasks.html)
- [Debugging PHP](https://www.jetbrains.com/help/phpstorm/debugging-php-applications.html)

## IDE Não Recomendadas

As seguintes IDEs NÃO são configuradas para este projeto:

| IDE | Motivo |
|-----|--------|
| VSCode | Falta plugins nativos de Laravel |
| Sublime Text | Debugging manual/complicado |
| Vim/Neovim | Sem suporte Docker nativo |
| JetBrains Fleet | Ainda em desenvolvimento |

---

**Nota**: Outros editores podem ser usados para contribuições pontuais, mas a experiência de desenvolvimento é otimizada para PHPStorm.

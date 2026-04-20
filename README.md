# Advanced Banners Manager

Plugin WordPress desenvolvido para resolver uma necessidade interna real de negócio: gerenciar banners promocionais em um e-commerce com regras de exibição, agendamento, múltiplos itens por banner e acompanhamento de interações.

## Contexto

Este plugin foi criado para atender um cenário específico da empresa em que soluções prontas do mercado não cobriam adequadamente a operação.

A necessidade envolvia:

- gerenciamento centralizado de banners no painel do WordPress
- múltiplos itens por banner
- suporte a imagens desktop e mobile
- agendamento por data e hora
- exibição por shortcode no front-end
- registro de visualizações e cliques

O resultado foi uma solução própria, orientada ao fluxo real do e-commerce.

## Funcionalidades

- cadastro e edição de banners no painel administrativo
- múltiplos itens por banner
- imagens para desktop e mobile
- agendamento por data e hora
- suporte a carrossel
- shortcode para exibição no front-end
- registro de visualizações e cliques
- logs de interação

## Estrutura do plugin

- `advanced-banners.php` — bootstrap do plugin
- `includes/class-ab-activator.php` — criação e atualização das tabelas
- `includes/class-ab-admin.php` — telas e assets do painel administrativo
- `includes/class-ab-ajax.php` — ações AJAX de gerenciamento e rastreamento
- `includes/class-ab-shortcode.php` — renderização do shortcode
- `includes/class-ab-logger.php` — persistência dos logs
- `views/` — templates do admin e front-end
- `assets/` — scripts e estilos

## Requisitos

- WordPress
- PHP 7.4+
- jQuery / jQuery UI no ambiente administrativo

## Instalação

1. Copie a pasta do plugin para `/wp-content/plugins/`
2. Ative o plugin no painel do WordPress
3. Acesse o menu **Banners** no painel administrativo
4. Crie banners e utilize o shortcode no front-end

## Exemplo de shortcode

```php
[banners slug="homepage-ofertas"]
```

Ou buscando diretamente pelo ID do banner:

```php
[banners id="12"]
```

## Observações

Este plugin foi projetado para um ambiente real de produção. Dependendo do tema, da estrutura do e-commerce e do fluxo editorial, alguns ajustes de integração podem ser necessários.

# Sistema de Gerenciamento de Tarefas

## Descrição
Sistema completo para gerenciamento de tarefas com autenticação de usuários, recuperação de senha e interface responsiva.

## Requisitos do Sistema
- XAMPP 7.4+ ou servidor web equivalente
- PHP 7.3+
- MySQL 5.7+

## Instalação
1. Importar o arquivo `database.sql` para o MySQL
2. Configurar dados de acesso no `config.php`
3. Colocar os arquivos no diretório `htdocs` do XAMPP

## Funcionalidades Principais
- Registro e autenticação de usuários
- Recuperação de senha via token seguro
- Dashboard para gestão de tarefas
- Interface limpa sem dependências externas (Bootstrap)

## Estrutura de Arquivos
```
├── config.php         # Configurações do banco de dados
├── login.php         # Autenticação de usuários
├── register.php      # Registro de novos usuários
├── recuperar_senha.php # Sistema de recuperação de senha
├── dashboard.php     # Interface principal
└── database.sql      # Estrutura do banco de dados
```

## Tecnologias Utilizadas
- PHP PDO para acesso seguro ao banco de dados
- MySQL para armazenamento de dados
- HTML5/CSS3 vanilla para interface
- Sessions para gestão de autenticação

## Capturas de Tela 
![image](https://github.com/user-attachments/assets/2b4f5e95-f348-4420-93cc-cbeeb365b840)

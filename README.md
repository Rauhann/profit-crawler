# Sobre

API para buscar lucro de empresas baseado em crawler. O arquivo na raiz do projeto "Makefile" é um encurtador de comandos. Para usá-lo, basta instalar a dependencia make, caso use Linux, e utilize para as instruções a seguir.

Caso não tenha instalado e não consiga usá-lo, basta encontrar o comando correspondente no "Makefile" e executar manualmente os comandos.

# Instruções de uso

### Referências

1 - Ter instalado no computador o Docker: https://docs.docker.com/engine/install/

2 - Ter instalado no computador o Docker Compose: https://docs.docker.com/compose/install/

### Linux ou WSL2

1 - Duplique o arquivo .env.example para um novo chamado ".env"

2 - "make build"

3 - Em outra aba de terminal, execute "make sh" para acessar o container

4 - Em outra aba de terminal, execute "make db" para acessar o banco de dados

### Windows

1 - Duplique o arquivo .env.example para um novo chamado ".env"

3 - "docker-compose up --build"

4 - Em outra aba de terminal, execute "docker exec -it -u dev profit-crawler-app /bin/bash" para acessar o container e execute o comand 'make sh' ou php artisan migrate

5 - Em outra aba de terminal, execute "docker exec -it profit-crawler-db bash -c "mysql -u ${DB_USERNAME} -p'${DB_PASSWORD}' ${DB_DATABASE}" para acessar o banco de dados


### Testes

1 - Execute "php artisan test", dentro do container, para executar todos os testes

2 - Execute "php artisan test --filter='nome da classe ou método'" para executar o teste de maneira individual

## Acesso

1 - Acesse no seu browser http://localhost

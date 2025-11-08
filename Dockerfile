# Use a imagem base oficial do PHP 8.3
FROM php:8.3-cli-alpine

# Instale o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Defina o diretório de trabalho
WORKDIR /app

# Copie os arquivos do projeto e instale as dependências
# Copiamos primeiro o composer.json para aproveitar o cache do Docker
COPY composer.json .
RUN composer install --no-dev --optimize-autoloader

# Copie o restante do código-fonte
COPY . .

# O entrypoint padrão será 'php'
CMD [ "php" ]

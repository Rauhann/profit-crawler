FROM php:8.3-fpm

# Usuário
RUN useradd -m -s /bin/bash dev

# Setando diretório de trabalho
WORKDIR /app

# Instalação do composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.2.12

# Instalação das dependências Laravel e PHP
RUN apt-get update \
	&& apt-get install -y \
    build-essential \
    openssl \
    libzip-dev \
    make \
    vim \
    unzip \
    curl \
    git \
    gifsicle \
    locales \
    libonig-dev \
    cron \
    supervisor \
	&& docker-php-ext-install pdo_mysql mbstring \
	&& docker-php-ext-install pdo \
	&& docker-php-ext-enable opcache \
    && docker-php-ext-install exif \
    && docker-php-ext-install sockets \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install bcmath \
	&& docker-php-ext-install zip \
	&& apt-get autoclean -y \
	&& rm -rf /var/lib/apt/lists/* \
	&& rm -rf /tmp/pear/

# Configuração de cron
COPY ./docker/cron/crontab /etc/cron.d/crontab
RUN chmod 0644 /etc/cron.d/crontab && \
    crontab /etc/cron.d/crontab

# Copiando arquivos para container
COPY --chown=dev:dev . /app
COPY --chown=dev:dev ./docker/supervisord/supervisord.conf /etc/supervisor/supervisord.conf
COPY --chown=dev:dev ./docker/bash/entrypoint.sh /sbin/entrypoint.sh

# Expondo porta de serviço PHP
EXPOSE 9000

# Script para inicialização
ENTRYPOINT ["/sbin/entrypoint.sh"]

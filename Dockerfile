# Image de base : PHP 8.2 avec PHP-FPM (le "moteur" qui exécute le code PHP)
FROM php:8.2-fpm

# Dépendances système nécessaires pour compiler les extensions PHP dont Laravel a besoin :
# - libpq-dev       : requis pour parler à PostgreSQL (pdo_pgsql)
# - libzip-dev      : requis pour l'extension zip (Composer et Laravel s'en servent)
# - libpng-dev, libjpeg62-turbo-dev, libfreetype6-dev : requis pour l'extension gd (traitement d'images)
# - libonig-dev      : requis pour l'extension mbstring (gestion des chaînes de caractères, utilisée partout dans Laravel)
# - libxml2-dev      : requis pour les extensions xml/dom
# - libcurl4-openssl-dev : requis pour l'extension curl (appels HTTP sortants)
# - unzip, git       : nécessaires à Composer pour installer les paquets PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Configure puis installe les extensions PHP dont Laravel a besoin.
# pdo_pgsql / pgsql : communication avec PostgreSQL
# mbstring, bcmath, zip, xml, exif, pcntl, curl : extensions standard requises par Laravel et ses dépendances
# gd : traitement d'images (utile même si non utilisé aujourd'hui, coût faible)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        bcmath \
        zip \
        xml \
        exif \
        pcntl \
        curl \
        gd

# Récupère l'exécutable Composer (gestionnaire de dépendances PHP) depuis son image officielle,
# sans avoir à l'installer nous-mêmes.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail dans le conteneur : c'est ici que vivra le code de l'application.
WORKDIR /var/www/html

# On copie tout le code du projet dans le conteneur.
COPY . .

# Installe les dépendances PHP du projet :
# --no-interaction        : ne pose aucune question (nécessaire car il n'y a personne pour répondre)
# --optimize-autoloader   : génère un chargement de classes plus rapide, adapté à la production
# --no-dev                : n'installe pas les paquets de développement (tests, debug, etc.)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Laravel a besoin d'écrire dans ces deux dossiers (logs, cache, fichiers compilés, sessions...).
# On donne la propriété à l'utilisateur "www-data" (celui qui fait tourner PHP-FPM)
# et les droits d'écriture nécessaires.
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# PHP-FPM écoute sur le port 9000 par défaut (ce n'est pas un port web classique comme 80,
# un serveur web comme Nginx viendra s'y connecter plus tard).
EXPOSE 9000

# Commande lancée au démarrage du conteneur : démarre PHP-FPM.
CMD ["php-fpm"]

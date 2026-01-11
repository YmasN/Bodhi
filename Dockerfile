FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    python3 \
    python3-pip \
    python3-venv \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Add user for laravel application
RUN groupadd -g 1000 www && \
    useradd -u 1000 -ms /bin/bash -g www www

# Install Node.js dependencies
RUN npm install -g npm

# Set up Python virtual environment and install packages
RUN python3 -m venv /opt/venv
ENV PATH="/opt/venv/bin:$PATH"
RUN /opt/venv/bin/pip install --upgrade pip && \
    /opt/venv/bin/pip install rdkit Pillow

# Set permissions
RUN chown -R www:www /var/www

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
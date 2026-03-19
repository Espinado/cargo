# Cargo TMS

Копия проекта Fleet Manager, настроенная как отдельное приложение **Cargo TMS** с подключением к базе данных **cargo**.

## Отличия от Fleet Manager

- **Название приложения:** Cargo TMS
- **База данных:** `cargo` (те же параметры: MySQL, host 127.0.0.1, port 3306, user root, без пароля)
- **URL по умолчанию:** http://cargo-trans.test

## Первый запуск

1. **Создайте базу данных** (если ещё не создана):
   ```sql
   CREATE DATABASE cargo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Добавьте виртуальный хост** в Laragon (например, `cargo-trans.test` → папка `c:\laragon\www\cargo-trans\public`).

3. **Установите зависимости** (из папки `cargo-trans`):
   ```bash
   composer install
   npm install
   npm run build
   ```

4. **Выполните миграции:**
   ```bash
   php artisan migrate
   ```

5. (Опционально) Сгенерировать новый ключ приложения:
   ```bash
   php artisan key:generate
   ```

## Конфигурация (.env)

Основные параметры уже заданы в `.env`:

- `APP_NAME="Cargo TMS"`
- `APP_URL=http://cargo-trans.test`
- `DB_CONNECTION=mysql`
- `DB_DATABASE=cargo`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_USERNAME=root`
- `DB_PASSWORD=` (пусто)

Измените при необходимости под свой окружение.

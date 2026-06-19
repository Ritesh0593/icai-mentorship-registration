# MSME & Startup Mentorship 2026 Registration System

A premium Laravel-based event registration system designed for the Institute of Chartered Accountants of India (ICAI) to manage multi-city event registrations, generate dynamically encoded URLs with printable SVG QR codes, and provide a full statistics dashboard.

---

## 🌟 Key Features

1. **Obfuscated City URLs**: Prevents guest guessing by using secure, unique, randomly generated 10-character slugs (e.g., `/register/6fPNNLldXG`).
2. **Auto QR Code Engine**: Instantly generates scalable, high-resolution SVG QR codes for cities upon creation. SVGs are vector-based, print-ready, and require no heavy `imagick` extensions on the server.
3. **Interactive Mock OTP Verification**: Form is securely locked on the public frontend until the attendee inputs their email ID and verifies it with the mock code `123456`.
4. **Interactive Dashboard**: Dynamic stats cards showing active cities, registrations, and today's signups, with an interactive comparison chart using Chart.js.
5. **Attendee Records & Search**: Tabular viewer with advanced, instant filtering by city and text search (wildcard matching on Name, Email, and Phone).
6. **Optimized CSV Streaming**: Memory-efficient database chunking to stream downloads of attendee lists (UTF-8 BOM encoded for direct Excel compatibility).
7. **Branded Custom layouts**: Styled specifically to match the client's mockup alignment guidelines, including inline headers and side-by-side grids.

---

## 💻 Local Setup (MAMP / XAMPP / Native)

1. **Clone the repository**:
   ```bash
   git clone https://github.com/Ritesh0593/icai-mentorship-registration.git qr
   cd qr
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Database Configuration**:
   - Create a database named `qr` in phpMyAdmin/Sequel Pro.
   - Copy `.env.example` to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Update your database credentials in `.env` (Port `8889` for MAMP, `3306` for default MySQL):
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=8889
     DB_DATABASE=qr
     DB_USERNAME=root
     DB_PASSWORD=root
     ```

4. **App Initialization**:
   ```bash
   php artisan key:generate
   php artisan storage:link
   ```

5. **Run Migrations & Seed Mock Data**:
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Serve Locally**:
   ```bash
   php artisan serve
   ```
   - Public Form: `http://127.0.0.1:8000/register/{slug}`
   - Admin Login: `http://127.0.0.1:8000/admin/login` (Password: `admin123`)

---

## 🚀 Live Server Deployment Guide (Ubuntu / Apache)

Follow these steps to deploy this project live on an AWS EC2 instance or VPS:

### 1. File Upload & Server Configuration
- Clone or pull your code into `/var/www/html/etechmy.com/ICAI/`.
- Ensure directory permissions are set up correctly for the web server user (typically `www-data`):
  ```bash
  cd /var/www/html/etechmy.com/ICAI/
  mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
  sudo chown -R www-data:www-data storage bootstrap/cache
  sudo chmod -R 775 storage bootstrap/cache
  ```

### 2. Dependencies & Database Setup
- Install dependencies:
  ```bash
  composer install --no-dev --optimize-autoloader
  ```
- Copy `.env`, run `php artisan key:generate`, and configure your live database credentials.
- Generate symbolic storage link:
  ```bash
  php artisan storage:link
  ```
- Run migrations:
  ```bash
  php artisan migrate --force
  ```

### 3. URL Clean Rewrite (Removing `/public`)
The project contains a root-level `.htaccess` file that automatically redirects all incoming traffic to `/public/` internally. 
- You can directly access the URLs without `/public` in the path:
  - **Registration URL**: `https://yourdomain.com/ICAI/register/{slug}`
  - **Admin Login**: `https://yourdomain.com/ICAI/admin/login`

### 4. Remote Cache Clearing
If you update configs or template files and need to recompile Blade files, simply hit the following URL in your browser:
👉 `https://yourdomain.com/ICAI/clear-cache`

---

## 🔒 Security Configuration
- The admin dashboard password is set in your `.env` file under `ADMIN_PASSWORD`:
  ```env
  ADMIN_PASSWORD=your_secure_password_here
  ```
- Make sure to disable `APP_DEBUG` in production (`APP_DEBUG=false` in `.env`) to prevent exposing system details on errors.

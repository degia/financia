# Financia — Personal Financial Management

Aplikasi manajemen keuangan pribadi berbasis web yang dibangun dengan **Laravel 12**, **TiDB Cloud**, dan **Tailwind CSS**. Membantu mencatat transaksi, mengelola anggaran, melacak utang/piutang, dan memantau tujuan keuangan.

---

## Fitur

| Modul | Deskripsi |
|-------|-----------|
| **Dashboard** | Ringkasan keuangan: saldo, pendapatan/pengeluaran bulanan, grafik, progres anggaran, transaksi terbaru. Kartu dapat di-drag-and-drop & disembunyikan. |
| **Accounts** | Kelola rekening bank, e-wallet, tabungan, dan cash. Saldo otomatis terupdate setiap transaksi. |
| **Transactions** | Catat pemasukan & pengeluaran dengan kategori dan sub-kategori. Dukungan filter tanggal, jenis, akun. |
| **Transfers** | Transfer antar akun (termasuk transfer ke tabungan). |
| **Categories** | Kategori transaksi (income/expense) dengan sub-kategori. |
| **Budgets** | Anggaran per kategori per bulan dengan notifikasi batas. |
| **Goals** | Target menabung dengan fitur kontribusi & progres. |
| **Loans** | Kelola utang (borrow) & piutang (lend) dengan pencatatan pembayaran. Status otomatis berubah saat lunas. |
| **Institutions** | Database bank/ewallet/dll dengan logo. Dikelola via Settings. |
| **Reports** | Laporan keuangan dalam bentuk CSV & PDF. |
| **Settings** | Preferensi pengguna: mata uang, visibilitas menu navigasi, default akun/kategori. |
| **Dark Mode** | Toggle dark/light mode, tersimpan di localStorage dengan fallback sistem. |

---

## Tech Stack

- **Backend:** PHP 8.2, Laravel 12
- **Database:** TiDB Cloud (MySQL-compatible), SSL enforced
- **Frontend:** Blade, Tailwind CSS, Alpine.js
- **Charts:** Chart.js
- **Auth:** Laravel Breeze (Blade stack)
- **Testing:** PHPUnit (168+ tests)

---

## Requirements

- PHP 8.2+
- Composer
- Node.js & npm
- TiDB Cloud Serverless instance (or any MySQL-compatible database)

---

## Installation

### 1. Clone & Install Dependencies

```bash
git clone <repo-url> financia
cd financia
composer install
npm install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` dengan konfigurasi database TiDB Cloud:

```env
DB_CONNECTION=mysql
DB_HOST=gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com
DB_PORT=4000
DB_DATABASE=financia
DB_USERNAME=<your-username>
DB_PASSWORD=<your-password>
DB_URL=<your-full-connection-url>
```

> **SSL Certificate**: Download CA cert dari [ISRG Root X1](https://letsencrypt.org/certs/isrgrootx1.pem) dan set `MYSQL_ATTR_SSL_CA` di `config/database.php`.

### 3. Migrate & Seed

```bash
php artisan migrate --force
php artisan db:seed --class=InstitutionSeeder
php artisan storage:link
```

### 4. Build Frontend

```bash
npm run build
```

### 5. Start Development Server

```bash
composer run dev
```

Atau secara terpisah:

```bash
php artisan serve
npm run dev
```

---

## Database Schema

### Tables (19 migrations)

| Table | Key Columns |
|-------|-------------|
| `users` | name, email, currency_preference, preferences (JSON) |
| `accounts` | user_id, institution_id, name, type, category, initial_balance, current_balance |
| `categories` | user_id, name, type, is_system |
| `sub_categories` | category_id, name |
| `transactions` | user_id, account_id, category_id, sub_category_id, transfer_id, loan_id, amount, type, date, is_savings |
| `budgets` | user_id, category_id, amount, month, year |
| `goals` | user_id, name, target_amount, current_amount, target_date |
| `institutions` | name, type, slug, logo, color, is_active |
| `loans` | user_id, account_id, name, type, amount, paid_amount, remaining_amount, interest_rate, status |
| `loan_payments` | loan_id, account_id, transaction_id, amount, payment_date |

---

## Architecture

### Key Patterns

- **Service Layer**: `TransactionService`, `DashboardService`, `AccountService`, `ReportService` memisahkan business logic dari controller.
- **Authorization**: 6 policies (`AccountPolicy`, `CategoryPolicy`, `TransactionPolicy`, `BudgetPolicy`, `GoalPolicy`, `LoanPolicy`) — ownership-based.
- **Alpine.js Components**: Drag-and-drop dashboard cards, dark mode toggle, dynamic form filtering.
- **Transfer System**: Transfers create paired expense/income transactions linked via `transfer_id`. Savings transfers use `is_savings = true` flag.

### Important Query Pattern

Semua query agregat pengeluaran menggunakan filter khusus untuk mengecualikan transfer internal tetapi tetap menyertakan transfer tabungan:

```php
->where(function ($q) {
    $q->whereNull('transfer_id')->orWhere('is_savings', true);
})
```

---

## Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter=TransactionTest
```

Test coverage: 54 unit tests (model relationships/casts) + 94+ feature tests (CRUD + authorization).

---

## Routes

### Authenticated Routes

| Method | URI | Controller |
|--------|-----|------------|
| GET | `/dashboard` | `DashboardController@index` |
| Resource | `/accounts` | `AccountController` |
| Resource | `/categories` | `CategoryController` |
| Resource | `/transactions` | `TransactionController` |
| Resource | `/budgets` | `BudgetController` |
| Resource | `/goals` | `GoalController` |
| Resource | `/loans` | `LoanController` |
| Resource | `/institutions` | `InstitutionController` |
| GET/POST | `/transfers` | `TransferController` |
| GET | `/reports` | `ReportController` |
| GET | `/reports/export/csv` | `ReportController@exportCsv` |
| GET | `/reports/export/pdf` | `ReportController@exportPdf` |
| POST | `/goals/{goal}/contribute` | `GoalController@contribute` |
| POST | `/loans/{loan}/payment` | `LoanController@payment` |
| DELETE | `/loans/{loan}/payment/{payment}` | `LoanController@destroyPayment` |
| POST/PUT/DELETE | `/sub-categories` | `SubCategoryController` |
| GET/PATCH | `/settings` | `SettingsController` |

---

## Key Design Decisions

- **Transfer transactions** tetap menggunakan `type = 'expense'` / `'income'` untuk akurasi saldo; filter `whereNull('transfer_id')` digunakan di laporan.
- **Savings transfers** menggunakan `is_savings = true`; masuk dalam perhitungan expense di laporan.
- **Loan integration**: Expense transaction dapat di-link ke loan → otomatis membuat `LoanPayment` dan mengurangi `remaining_amount`.
- **Institutions** dikelola sebagai data master (bukan static config) agar user bisa upload logo sendiri.
- **Preferences** disimpan di JSON column `users.preferences` (menu visibility, defaults).

---

## Deployment Notes

- TiDB Cloud Serverless: SSL wajib diaktifkan via `MYSQL_ATTR_SSL_CA`
- Environment variables: pastikan `.env` tidak masuk version control (sudah di `.gitignore`)
- Storage link: `php artisan storage:link` diperlukan untuk akses logo institusi
- Queue: tidak digunakan untuk MVP, tapi tersedia jika diperlukan
- Session: menggunakan driver database (table `sessions`)

---

## License

Hak cipta dilindungi undang-undang.

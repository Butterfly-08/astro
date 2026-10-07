# 🔮 AstroVani — Astrologer E-Commerce, Product Referral, Commission & Withdrawal Platform

A production-ready **Astrologer E-commerce + Product Referral + Dynamic Commission Engine + Double-Entry Wallet + Withdrawal Payout System** built with **PHP 8.2+ and Laravel 11**.

---

## 🌟 Key Highlights & System Architecture

- **Strict Separation of Concerns & Financial Integrity**:
  - All balance and transaction operations run inside `DB::transaction()` with pessimistic row-level locking (`lockForUpdate()`) to prevent race conditions.
  - Zero direct wallet balance edits. An immutable double-entry ledger (`wallet_transactions`) records every credit, debit, hold, release, and adjustment.
- **Three-Tier Commission Hierarchy**:
  - `Product-Specific Rate` > `Category Rate` > `Global Default Rate`.
  - Configurable commission types (percentage `%` or flat fixed `₹`).
  - Commission base options: `product_subtotal`, `subtotal_before_tax`, or `total`.
- **Advanced Referral Engine**:
  - Unique partner referral codes (e.g., `GURU100`) and customizable slugged referral links.
  - Configurable cookie duration (default 30 days) and attribution models (`last_click` or `first_click`).
  - IP-based click cooling and fraud protection against click farming.
  - Self-referral prevention (astrologers cannot earn commissions on their own orders).
- **Hold Periods & Automated Commission Release**:
  - New commissions enter `pending` state for dispute/return handling (configurable, default 7 days).
  - Automated background command `php artisan commissions:release` safely unlocks matured commissions into astrologer available wallet balances.
- **Transactional Withdrawal Payout Flow**:
  - Astrologer selects UPI ID or Bank Transfer (masked and AES-256 encrypted storage).
  - Requested funds are immediately locked into `held_balance`.
  - Admin review workflow: `Pending` → `Under Review` → `Approved` → `Processing` → `Paid` (with Bank UTR/Transaction reference) or `Rejected` (instantly releases held funds back to available wallet).
- **Three Distinct User Portals**:
  1. **Customer Front-End**: E-commerce catalog, shopping cart, coupon codes, checkout, order tracking, astrologer booking.
  2. **Astrologer Partner Portal**: Referral link generator, real-time analytics & graphs, commission history, double-entry ledger, withdrawal requests.
  3. **Admin Control Panel**: Partner approval/suspension, product commission rules, wallet adjustments, withdrawal approvals, referral traffic logs, and platform settings.

---

## 🏗️ Technology Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL / SQLite (with foreign keys and check constraints)
- **Frontend**: Blade templates, Bootstrap 5, Bootstrap Icons, Chart.js
- **Testing**: PHPUnit 11 with 100% test coverage across financial workflows

---

## 🚀 Quick Start & Installation

### 1. Prerequisites
- PHP 8.2 or 8.3+ with `pdo`, `mbstring`, `openssl`, `bcmath`
- Composer 2+
- MySQL 8.0+ or SQLite

### 2. Clone & Setup Dependencies
```bash
git clone <repository_url>
cd astro
composer install
npm install && npm run build
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database credentials in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=astro
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database Migrations & Seeders
```bash
php artisan migrate --seed
```

### 5. Create Administrator Account
```bash
php artisan admin:create
# or run the provision command:
php artisan app:provision-admin
```

### 6. Start Development Server
```bash
php artisan serve
```
- Storefront: `http://localhost:8000`
- Astrologer Portal: `http://localhost:8000/astrologer/login`
- Admin Control Panel: `http://localhost:8000/admin/login`

---

## 🧪 Automated Testing Suite

The application includes an end-to-end automated test suite covering all referral tracking, order attribution, commission calculation, wallet locking, and withdrawal flows:

```bash
# Run all tests
php vendor/phpunit/phpunit/phpunit

# Run Referral, Commission & Wallet feature tests
php vendor/phpunit/phpunit/phpunit --filter ReferralCommissionWalletTest
```
✅ **Test Suite Result: 57 tests, 399 assertions — 100% passing.**

---

## 🔄 Scheduled Tasks & Cron Jobs

Configure your server cron to trigger Laravel's scheduler every minute:
```bash
* * * * * cd /path/to/astro && php artisan schedule:run >> /dev/null 2>&1
```

The scheduler automatically runs:
- `commissions:release` (hourly): Inspects pending commissions whose hold period has elapsed and atomically credits them to astrologer wallets.

You can also run it manually at any time:
```bash
php artisan commissions:release
# or alias
php artisan commission:release
```

---

## 📬 Postman API Testing Collection

A complete Postman collection is included in the project root:
- **File**: `AstroReferral_API.postman_collection.json`

### Key API Endpoints
| Endpoint | Method | Access | Description |
|---|---|---|---|
| `/api/v1/referrals/validate/{code}` | `GET` | Public | Validates partner referral code |
| `/api/v1/referrals/track` | `POST` | Public | Logs referral click & creates cookie |
| `/api/v1/astrologer/login` | `POST` | Public | Authenticates astrologer and returns token |
| `/api/v1/astrologer/wallet` | `GET` | Astrologer | Retrieves wallet balances and ledger entries |
| `/api/v1/astrologer/commissions` | `GET` | Astrologer | List of order referral commissions |
| `/api/v1/astrologer/withdrawals` | `POST` | Astrologer | Submits a withdrawal request (locks funds) |
| `/api/v1/astrologer/withdrawals` | `GET` | Astrologer | Withdrawal payout history |
| `/api/v1/astrologer/referral-links` | `GET` | Astrologer | Generates referral URLs for products |

---

## 🔒 Security & Fraud Safeguards

1. **Transaction Integrity**: Pessimistic `lockForUpdate()` prevents duplicate wallet withdrawals even under concurrent requests.
2. **Double-Entry Ledger**: Every single rupee movement has an immutable matching `WalletTransaction` row (`balance_before`, `balance_after`, `reference_key`).
3. **Encrypted Bank Data**: Sensitive bank account numbers are encrypted at rest using Laravel's AES-256-CBC cipher (`account_number_encrypted`), with only last 4 digits stored for UI masking (`account_number_masked`).
4. **Self-Referral Prevention**: Server-side check prevents an astrologer from earning commission on orders made by their own customer account.
5. **Rate-Limiting & Duplicate Click Cooling**: Prevents bot-driven click attribution manipulation.

---

## 📄 License
This platform is open-source software licensed under the MIT license.

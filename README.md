# 📦 Sistem Informasi Manajemen Aset & Service Desk — AssetMS

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Database-SQLite%20%2F%20MySQL-blue?logo=mysql&logoColor=white" alt="Database">
  <img src="https://img.shields.io/badge/License-MIT-green" alt="License">
</p>

Aplikasi web manajemen inventaris aset perusahaan dan *service desk* berbasis **Laravel 12**, dilengkapi sistem otorisasi berbasis peran (RBAC), pelacakan mutasi aset, dashboard analitik interaktif, serta sistem tiket layanan IT dengan SLA dan auto-assignment.

---

## ✨ Fitur Utama

### Manajemen Aset
| Fitur | Deskripsi |
|-------|-----------|
| Dashboard Analitik | Grafik distribusi status, kategori aset, trend mutasi 6 bulan, dan log mutasi *real-time* |
| Manajemen Aset | CRUD aset lengkap dengan kode unik otomatis berbasis kategori & tanggal |
| Mutasi Aset | Pencatatan perpindahan aset antar lokasi/pengguna dengan log otomatis via Observer |
| Manajemen Lokasi | Pengelolaan lokasi/ruangan/departemen tempat penyimpanan aset |
| Manajemen Kategori | Kategori aset dengan singkatan kode otomatis untuk *asset code generator* |
| Manajemen Merek & Vendor | Pengelolaan data brand dan vendor aset |
| RBAC Granular | Permission berbasis Spatie untuk kontrol akses setiap fitur |
| Privasi Finansial | Data harga & tanggal beli hanya tampil untuk user dengan izin finansial |

### Service Desk (Tiket IT)
| Fitur | Deskripsi |
|-------|-----------|
| Tiket Layanan | Pembuatan dan pengelolaan tiket IT (CRUD + workflow status) |
| SLA Management | Kebijakan SLA per prioritas dengan perhitungan jam kerja (business hours) |
| Auto-Assignment | Penugasan tiket otomatis ke agen berdasarkan spesialisasi & beban kerja |
| Workflow Status | 9 status tiket dengan state machine & role-based transitions |
| Agent Management | Status agen (Available/Busy/Away/Offline) dan spesialisasi kategori |
| Komentar Internal | Komentar publik & internal pada tiket |
| Audit Log | Riwayat perubahan tiket (status, prioritas, agen) secara otomatis |
| Eskalasi | Eskalasi tiket otomatis jika terjadi SLA breach |
| Reports & Performa | Laporan tiket dan performa agen |

---

## 🔐 Sistem Hak Akses

Sistem menggunakan **Spatie Laravel Permission** dengan tiga role utama:

### Role: `admin` (Super Admin)
- Akses penuh ke seluruh sistem
- Mengelola permission setiap user
- Melihat data finansial aset

### Role: `agent` (Service Desk Agent)
- Mengelola tiket yang ditugaskan
- Mengubah status tiket sesuai workflow
- Tidak bisa menghapus tiket
- Melihat data aset (kecuali finansial)

### Role: `staff`
Permission staff dikelola **secara individual** oleh Super Admin:

#### Permission Modul Aset
| Permission | Akses |
|---|---|
| `asset.viewAny` | Melihat daftar aset |
| `asset.create` | Membuat aset baru |
| `asset.edit` | Mengedit data aset |
| `asset.delete` | Menghapus aset |
| `asset.manage_finances` | Input/lihat harga & tanggal beli |
| `asset.mutate` | Mutasi/perpindahan aset |

#### Permission Modul Service Desk
| Permission | Akses |
|---|---|
| `ticket.viewAny` | Melihat daftar tiket |
| `ticket.create` | Membuat tiket baru |
| `ticket.assign` | Menugaskan tiket ke agen |
| `ticket.manage` | Mengelola tiket (edit prioritas, kategori) |
| `ticket.close` | Menutup tiket |
| `ticket.delete` | Menghapus tiket |
| `ticket.reports` | Akses laporan & performa agen |

#### Permission Modul Pendukung
| Permission | Akses |
|---|---|
| `location.{viewAny,create,edit,delete}` | Manajemen lokasi |
| `category.{viewAny,create,edit,delete}` | Manajemen kategori |
| `brand.{viewAny,create,edit,delete}` | Manajemen merek |
| `vendor.{viewAny,create,edit,delete}` | Manajemen vendor |

---

## 📁 Struktur Proyek

```
inventory-aset/
├── app/
│   ├── Console/Commands/
│   │   └── CheckSlaBreach.php               # Pengecekan SLA breach via artisan
│   ├── Enums/
│   │   ├── AgentStatus.php                  # Available, Busy, Away, Offline
│   │   ├── AssetStatus.php                  # InUse, Spare, Service, Broken, Disposal, BrokenCheck
│   │   ├── TicketPriority.php               # Urgent, High, Medium, Low
│   │   ├── TicketSource.php                 # Web, Email, WhatsApp, Phone
│   │   ├── TicketStatus.php                 # Draft s/d Reopen (9 status)
│   │   └── UserRole.php                     # Admin, Agent, Staff
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php      # Dashboard utama agregasi data
│   │   │   ├── AssetController.php          # CRUD aset + permission filter
│   │   │   ├── BrandController.php          # CRUD merek
│   │   │   ├── CategoryController.php       # CRUD kategori aset
│   │   │   ├── LocationController.php       # CRUD lokasi
│   │   │   ├── UserController.php           # Manajemen user & permission
│   │   │   ├── VendorController.php         # CRUD vendor
│   │   │   ├── ProfileController.php        # Manajemen profil
│   │   │   └── ServiceDesk/                 # Modul Service Desk
│   │   │       ├── DashboardController.php
│   │   │       ├── TicketController.php
│   │   │       ├── CommentController.php
│   │   │       ├── ReportController.php
│   │   │       ├── SlaPolicyController.php
│   │   │       └── TicketCategoryController.php
│   │   ├── Middleware/
│   │   │   ├── CheckAdmin.php               # Admin-only route protection
│   │   │   └── CheckRole.php                # Generic role check
│   │   └── Requests/                        # 20 Form Request validasi
│   ├── Models/
│   │   ├── Asset.php                        # SoftDeletes + search scopes
│   │   ├── AssetCategory.php                # + abbreviation accessor/mutator
│   │   ├── AssetLocation.php                # Legacy location model
│   │   ├── AssetMutationLog.php             # Riwayat mutasi aset
│   │   ├── Brand.php
│   │   ├── Location.php                     # + slug auto-generation
│   │   ├── User.php                         # + isAdmin/isStaff/isAgent/isRequester helpers
│   │   ├── Vendor.php
│   │   ├── AgentSpecialization.php
│   │   ├── AgentStatusModel.php
│   │   ├── SlaPolicy.php
│   │   ├── Ticket.php                       # SoftDeletes + search scopes
│   │   ├── TicketCategory.php               # Hierarchical (parent-child)
│   │   ├── TicketComment.php
│   │   ├── TicketEscalation.php
│   │   ├── TicketLog.php
│   │   └── TicketSlaPause.php
│   ├── Observers/
│   │   ├── AssetObserver.php                # Auto-generate kode aset + log mutasi
│   │   └── TicketObserver.php               # Auto-generate no tiket + log SLA + audit
│   ├── Services/
│   │   ├── AssetCodeGenerator.php           # Format: AST{ABR}{YY}{MM}{SEQ}
│   │   ├── TicketCodeGenerator.php          # Format: TKT{YY}{MM}{SEQ}
│   │   ├── AutoAssignService.php            # Assign tiket ke agent terbaik
│   │   ├── ServiceDeskDashboardService.php  # Statistik dashboard Service Desk
│   │   ├── SlaCalculator.php                # Hitung deadline SLA (business hours)
│   │   ├── TicketLogService.php             # Helper pencatatan log
│   │   └── TicketStatusWorkflow.php         # State machine workflow status
│   └── View/Components/
│       ├── AppLayout.php
│       └── GuestLayout.php
├── config/
│   └── permission.php                       # Spatie Permission config
├── database/
│   ├── migrations/                          # 25 migration files
│   └── seeders/                             # 9 seeder classes
├── resources/views/
│   ├── admin/{brands,categories,locations,users,vendors}/
│   ├── assets/
│   ├── auth/
│   ├── components/
│   ├── dashboard/
│   ├── layouts/
│   ├── profile/
│   ├── service-desk/{categories,reports,sla-policies,tickets}/
│   ├── dashboard.blade.php
│   └── welcome.blade.php
├── routes/
│   ├── web.php                              # Routes utama (aset, admin, service-desk)
│   ├── auth.php                             # Routes autentikasi
│   └── console.php                          # Artisan commands
└── tests/
    ├── Feature/
    │   ├── Auth/                            # 6 test files autentikasi
    │   ├── AssetCodeGeneratorTest.php
    │   ├── AssetMutationAndPrivacyTest.php  # 9 test cases
    │   ├── ProfileTest.php
    │   └── ExampleTest.php
    └── Unit/
        └── ExampleTest.php
```

---

## 🚀 Cara Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd inventory-aset
```

### 2. Install Dependensi
```bash
composer install
npm install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

Konfigurasi database di `.env`:
```env
DB_CONNECTION=sqlite
# atau untuk MySQL:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_aset
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi & Seed Database
```bash
php artisan migrate
php artisan db:seed
```

### 5. Build Frontend
```bash
npm run build
```

### 6. Jalankan Server
```bash
php artisan serve
```

Atau gunakan dev script (server + queue + vite concurrently):
```bash
composer run dev
```

Akses di browser: `http://localhost:8000`

---

## 👤 Akun Default (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@company.com | password123 |
| Agent | agent@company.com | password123 |
| Staff | staff@company.com | password123 |

> Ganti password setelah login pertama di lingkungan produksi!

---

## 📊 Dashboard

### Dashboard Utama (Manajemen Aset)
- **6 Kartu Metrik Utama**: Total Aset, Digunakan, Cadangan, Servis, Bermasalah, dan Nilai Total Aset
- **Doughnut Chart**: Distribusi status aset
- **Bar Chart**: Jumlah aset per kategori (maks. 8 kategori teratas)
- **Line Chart**: Trend mutasi aset 6 bulan terakhir
- **Log Mutasi Terbaru**: 10 aktivitas mutasi terkini
- **Tiket Terbaru**: 5 tiket yang baru diajukan

### Dashboard Service Desk
- **Kartu Metrik**: Tiket Open, Tiket Hari Ini, Unassigned, SLA Breach, Urgent
- **Distribusi Status Tiket**: Doughnut chart
- **Tiket Terbaru**: Daftar tiket terkini yang perlu ditindaklanjuti

---

## 🧪 Testing

Jalankan seluruh test suite:
```bash
php artisan test
```

Atau test spesifik:
```bash
php artisan test tests/Feature/AssetMutationAndPrivacyTest.php
php artisan test tests/Feature/AssetCodeGeneratorTest.php
```

---

## ⚙️ Artisan Commands Kustom

| Command | Deskripsi |
|---------|-----------|
| `sd:check-sla` | Memeriksa tiket yang mendekati/melanggar SLA dan melakukan eskalasi otomatis |

---

## 🛠️ Tech Stack

- **Framework**: Laravel 12.x (PHP 8.2+)
- **Authentication**: Laravel Breeze (Session-based)
- **Authorization**: Spatie Laravel Permission v6
- **Database**: SQLite (dev) / MySQL (production)
- **Frontend**: Bootstrap 5.3, Bootstrap Icons, Tailwind CSS
- **Charts**: Chart.js 4.4
- **Build Tool**: Vite + PostCSS + Autoprefixer
- **Testing**: PHPUnit (Laravel Feature & Unit Test)

---

## 📝 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

<p align="center">
  AssetMS v2.0 &copy; {{ date('Y') }}
</p>

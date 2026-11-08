# Support Ticket & SLA Management System

A Laravel 10 application for customers to open support tickets and for support teams to assign, respond to, resolve, and monitor them against configurable SLA rules.

## Features

- Role-based access for administrators, support agents, and customers.
- Ticket creation with unique IDs in the format `TKT-YYYY-000001`.
- Ticket search, filters, pagination, lifecycle statuses, and audit history.
- Manual assignment, agent self-assignment, and automatic least-loaded-agent assignment. Assignment uses database row locks to prevent two agents from accepting the same ticket.
- Public replies, internal agent notes (hidden from customers), and protected attachment download/preview.
- Configurable categories, priorities, SLA rules, business hours, and holidays.
- Business-hours-aware SLA deadlines, five-minute SLA monitoring, escalation records, and activity entries.
- Database and email ticket-event notifications.
- Streaming CSV ticket import with row-level validation and error reporting.
- Role-specific dashboards.

## Requirements

- PHP 8.1 or later with the extensions required by Laravel and MySQL 
- Composer 2.
- Node.js 18.
- MySQL 8.0.

## Local setup

1. Clone the repository and enter its directory.

   ```bash
   git clone <repository-url>
   cd support-ticket-and-sla-management-system
   ```

2. Install dependencies.

   ```bash
   composer install
   npm ci
   ```

3. Create the environment file and application key.

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   On Windows PowerShell, use:

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

4. Create a MySQL database, for example `support_ticket_sla`, then update `.env`:

   ```dotenv
   APP_NAME="Support Ticket & SLA Management"
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=support_ticket_sla
   DB_USERNAME=root
   DB_PASSWORD=

   QUEUE_CONNECTION=database
   ```

5. Run migrations and seed the initial configuration and demo users.

   ```bash
   php artisan migrate --seed
   ```

6. Build front-end assets.

   ```bash
   npm run build
   ```

7. In separate terminals, start the web server, queue worker, and scheduler.

   ```bash
   php artisan serve
   php artisan queue:work --tries=3
   php artisan schedule:work
   ```

   Open `http://127.0.0.1:8000`.

## Background processing

CSV imports and `TicketEventNotification` implement Laravel's queue contract. Set `QUEUE_CONNECTION=database`, run the migrations (which create `jobs` and `failed_jobs`), and keep a worker running:

```bash
php artisan queue:work --tries=3
```

The SLA monitor is scheduled every five minutes. For local development use `php artisan schedule:work`. On a server, configure one cron entry:

```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

To run the SLA check manually:

```bash
php artisan tickets:monitor-sla
```

## Email configuration

Ticket notifications are delivered through Laravel's mail configuration. For local testing, use a Mailpit instance or configure a real SMTP provider. Example Mailpit values:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="support@example.test"
MAIL_FROM_NAME="${APP_NAME}"
```

All ticket-event notifications and CSV imports are processed by the queue worker. Review failed jobs with:

```bash
php artisan queue:failed
```


## SLA and assignment rules

The seeder creates Low (48h), Medium (24h), High (8h), and Critical (2h) SLA rules. Deadlines use configured working days, working hours, and holidays rather than simple wall-clock addition. Administrators can change these settings in the application.

Automatic assignment selects the active agent with the fewest active tickets, breaking ties by user ID. Ticket and agent row locks are used inside database transactions to make competing assignment attempts safe.

## CSV import

Administrators can upload CSV files up to 20 MB. The importer streams the file instead of loading it completely into memory, validates each row, and records invalid rows against the import. A row is treated as a duplicate and skipped when a ticket already exists with the same requester, subject, description, category, and priority. Unexpected job-level failures mark the import as `failed` and show the failure reason.

Required headers:

```text
requester_email,subject,description,category_id,priority_id
```

The requester must already exist and have the `customer` role. Categories and priorities must exist.

## Tests

Run the automated test suite with:

```bash
php artisan test
```


# SW Reserv

SW Reserv is a **SaaS reservation system** built with Laravel.

It supports multi-store operations, reservation lifecycle management, customer-facing APIs, and SMS messaging workflows for welcome, notice, and cancellation updates.

## Core Capabilities

- Multi-tenant store context (`storeCode`) with store-specific settings.
- Reservation management with status and SMS state tracking.
- Customer APIs for reservation availability, booking, listing, and cancellation.
- Queue and calendar operations for in-store staff workflows.
- Store Admin and Super Admin portals.
- Twilio SMS integration with per-store sender numbers (`twilio_from`).

## SMS Workflow (High Level)

- **Welcome SMS**: sent for new waiting reservations.
- **Notice SMS**: sent close to reservation time.
- **Cancel SMS**: sent when a reservation is canceled.
- SMS content comes from store-level templates when configured, otherwise fallback templates are used.
- Inbound SMS replies are handled by `/sms/receive`.

## Important Routes

- Customer reservation API:
  - `GET /api/v1/stores/{storeCode}/availability`
  - `POST /api/v1/stores/{storeCode}/reservations`
  - `GET /api/v1/stores/{storeCode}/reservations`
  - `POST /api/v1/stores/{storeCode}/reservations/{id}/cancel`
- SMS batch triggers:
  - `GET /{storeCode}/welcome`
  - `GET /{storeCode}/notice`
  - `GET /{storeCode}/cancel`

## Quick Start

1. Install dependencies:
   - `composer install`
2. Configure environment:
   - Copy `.env.example` to `.env`
   - Configure DB, central DB, and Twilio credentials
3. Generate app key:
   - `php artisan key:generate`
4. Run migrations (as required by your environment).
5. Start the app:
   - `php artisan serve`

## Production Notes

- Configure scheduled tasks (cron/Plesk Scheduled Tasks) to trigger:
  - `/{storeCode}/welcome`
  - `/{storeCode}/notice`
  - `/{storeCode}/cancel`
- Ensure each store has its own `twilio_from` configured in central `stores`.

## Tech Stack

- Laravel (PHP)
- MySQL
- Twilio SDK


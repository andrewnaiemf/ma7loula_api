# Order push notifications (FCM) — mobile integration

All order-related pushes use **FCM HTTP v1**. Payload includes a visible **`notification`** (`title` / `body`) and a **`data`** map (all values are strings).

Register the device token on **login/register** (`fcm_token` and/or `user_fcm_tokens`). Without a token, the API logs a skip and no push is sent.

### High-priority delivery (background/killed app)

All notifications include platform-specific settings for reliable delivery:

```json
{
  "message": {
    "notification": { "title": "...", "body": "..." },
    "data": { ... },
    "android": {
      "priority": "HIGH",
      "notification": {
        "channel_id": "order_alerts_channel",
        "sound": "default"
      }
    },
    "apns": {
      "headers": { "apns-priority": "10" }
    }
  }
}
```

**Android**: Ensure your app creates a notification channel with ID `order_alerts_channel` for proper sound and display.

**iOS**: `apns-priority: 10` enables immediate delivery even when the app is terminated.

---

## Which ID to open in the app

| App | Order type | Open screen using |
|-----|------------|-------------------|
| **Client** | car-parts, tire, battery, winch, emergency | `data.order_id` → `orders.id` |
| **Vendor** (car-parts / BT) | car-parts, tire, battery | `data.order_vendor_id` → `order_vendors.id` (vendor order list / `order/details?id=`) |
| **Vendor** (car-parts / BT) | winch, emergency (company account) | `data.order_vendor_id` if present, else `order_id` |
| **Worker** (winch / emergency driver) | winch, emergency | `data.order_id` → `orders.id` |

**Notification text numbering**

- **Customer** messages use **`orders.id`** in `title` / `body`.
- **Vendor (shop)** messages use **`order_vendors.id`** in `title` / `body` (car-parts / tires / batteries).
- **Worker (driver)** messages use **`orders.id`** in `title` / `body`.

---

## Common `data` fields

| Field | Description |
|-------|-------------|
| `title` | Same as notification title |
| `body` | Same as notification body |
| `event_type` | Use this to route in-app (see table below) |
| `action_required_for` | `customer` \| `vendor` \| `worker` |
| `kind` | Legacy alias; prefer `event_type` |
| `order_id` | `orders.id` |
| `order_vendor_id` | `order_vendors.id` (vendor line; may be empty for some worker-only events) |
| `status` | Main order status (`orders.status`) |
| `order_vendor_status` | Vendor line status when applicable |
| `type` | `car-parts` \| `tire` \| `battery` \| `winch` \| `emergency` |
| `vendor_id` | `vendors.id` |
| `worker_id` | `workers.id` (winch / emergency) |
| `offered_total` | Price string (listed total, counter-offer, or line total) |
| `decision` | `accept` \| `reject` (offer decision events only) |

---

## `event_type` reference

### Car-parts / tires / batteries (vendor shop + client)

| `event_type` | Recipient (`action_required_for`) | Trigger |
|--------------|-----------------------------------|---------|
| `new_order_created` | `vendor` | Client creates order (tires, batteries, car-parts) |
| `vendor_offer_created` | `customer` | Vendor submits counter-offer (`submit-price-offer`) |
| `customer_accepted_vendor_offer` | `vendor` | Client accepts offer (`respond-vendor-offer`, `action=accept`) |
| `customer_rejected_vendor_offer` | `vendor` | Client rejects offer (`respond-vendor-offer`, `action=reject`) |
| `vendor_updated_order_status` | `customer` | Vendor updates line status (`order/update-status`) |

### Winch / emergency (worker driver + client; vendor company also gets `new_order_created`)

| `event_type` | Recipient | Trigger |
|--------------|-----------|---------|
| `new_order_created` | `vendor` | Client creates winch/emergency (vendor company user) |
| `new_service_request` | `worker` | Client creates winch/emergency (all workers of that type) |
| `worker_offer_created` | `customer` | Worker sends offer (`winch` / `emergency` `order/send-offer`) |
| `customer_accepted_worker_offer` | `worker` | Client accepts driver offer |
| `customer_rejected_worker_offer` | `worker` | Client rejects driver offer |
| `order_status_updated` | `customer` | Worker updates status or emergency services/price updated |
| `customer_updated_order_status` | `worker` | Client updates order status (winch/emergency) |

---

## Suggested in-app routing (pseudo-code)

```text
onNotification(data):
  switch data.event_type:
    case 'new_order_created':
      if app == vendor: openVendorOrder(data.order_vendor_id)
      break
    case 'new_service_request':
      if app == winch|emergency worker: openOrder(data.order_id)
      break
    case 'vendor_offer_created':
    case 'worker_offer_created':
      if app == client: openOrder(data.order_id) // show offers
      break
    case 'customer_accepted_vendor_offer':
    case 'customer_rejected_vendor_offer':
      if app == vendor: openVendorOrder(data.order_vendor_id)
      break
    case 'customer_accepted_worker_offer':
    case 'customer_rejected_worker_offer':
      if app == worker: openOrder(data.order_id)
      break
    case 'vendor_updated_order_status':
    case 'order_status_updated':
      if app == client: openOrder(data.order_id)
      break
    case 'customer_updated_order_status':
      if app == worker: openOrder(data.order_id)
      break
```

---

## API endpoints that send pushes

### Client (`/api/v1/client/...`)

| Flow | Endpoint | Push |
|------|----------|------|
| Car-parts / tires / batteries | `POST .../create-order` | → vendor `new_order_created` |
| Car-parts / tires / batteries | `POST .../respond-vendor-offer` | → vendor accept/reject |
| Winch | `POST winch/create-order` | → workers `new_service_request`, vendors `new_order_created` |
| Winch | `POST winch/accept-offer` / `reject-offer` | → worker decision |
| Winch | `POST winch/update-order-status` | → worker `customer_updated_order_status` |
| Emergency | `POST emergency/create-order` | → workers + vendors (same pattern) |
| Emergency | `POST emergency/accept-offer` / `reject-offer` | → worker decision |
| Emergency | `POST emergency/update-order-status` | → worker |

### Vendor (`/api/v1/car-parts-vendor/...`, `/api/v1/bt-vendor/...`)

| Endpoint | Push |
|----------|------|
| `POST order/submit-price-offer` | → customer `vendor_offer_created` |
| `POST order/accept-line` | (no extra push; status sync only) |
| `POST order/update-status` | → customer `vendor_updated_order_status` |

### Worker (`/api/v1/winch/...`, `/api/v1/emergency/...`)

| Endpoint | Push |
|----------|------|
| `POST order/send-offer` | → customer `worker_offer_created` |
| `POST order/update-status` | → customer `order_status_updated` |
| Emergency `POST order/update-order-services` | → customer `order_status_updated` |

---

## Server requirements

- `.env`: `FIREBASE_CREDENTIALS`, `FIREBASE_PROJECT_ID`
- Order notification jobs run **synchronously** (no queue worker required for these pushes)
- Check `storage/logs/laravel.log` for `FCM skipped` or `FCM send failed` if pushes do not arrive

---

## Order status values (reference)

**`orders.status`:** `new`, `pending_customer`, `pending_vendor`, `accepted`, `processing`, `completed`, `cancelled`

**`order_vendors.status`:** `new`, `vendor_accepted`, `offer_pending`, `offer_declined`, `confirmed`, `completed`, `cancelled`

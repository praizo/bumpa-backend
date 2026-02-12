# Loyalty System Assessment

This project implements a full-stack **Loyalty Program** as an assessment feature, built with **Laravel 12**.

## 🚀 Features

- **Achievements**: Unlocked based on purchase count (e.g., "First Purchase", "Loyal Customer").
- **Badges**: Earned by unlocking multiple achievements (e.g., "Bronze Badge").
- **Event-Driven Architecture**: Uses Events (`PurchaseMade`, `AchievementUnlocked`) and Listeners (`CheckAchievements`, `CheckBadges`) to decouple logic.
- **API**: REST API for tracking progress and simulating transactions.

---

## 🛠️ Setup Instructions

1.  **Clone & Install Dependencies**:

    ```bash
    composer install
    ```

2.  **Environment Setup**:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

3.  **Database & Seeds**:
    This will set up the tables and populate default Achievements/Badges and a Test User (`test@example.com`).

    ```bash
    php artisan migrate:fresh --seed
    ```

4.  **Start Application**:
    ```bash
    php artisan serve
    # php artisan queue:work
    ```

---

## 🧪 Verification & Testing

### 1. Authentication

Get an access token for the seeded test user.

- **Endpoint**: `POST /api/login`
- **Body**:
    ```json
    {
        "email": "test@example.com",
        "password": "Password@123"
    }
    ```
- **Response**: Copy the `token` from the response.

### 2. Simulate Purchases (Trigger Logic)

Use this endpoint to simulate purchases and trigger the loyalty system logic.

- **Endpoint**: `POST /api/purchase`
- **Headers**:
    - `Authorization`: `Bearer <YOUR_TOKEN>`
    - `Content-Type`: `application/json`
- **Body**:
    ```json
    {
        "amount": 100
        // Optional: "items": [{"name": "Item A", "price": 100, "qty": 1}]
        // If "items" is omitted, it is auto-generated using Faker.
    }
    ```

### 3. Check Progress

View the user's current unlocked achievements and badges.

- **Endpoint**: `GET /api/users/1/achievements`
- **Headers**:
    - `Authorization`: `Bearer <YOUR_TOKEN>`
- **Response Structure**:
    ```json
    {
        "unlocked_achievements": ["Big Spender I"],
        "next_available_achievements": ["Big Spender II"],
        "current_badge": "None",
        "next_badge": "Gold Badge",
        "remaining_to_unlock_next_badge": 1,
        "next_achievement_progress": {
            "name": "Big Spender II",
            "required_spend": 30000,
            "remaining_spend": 20000
        }
    }
    ```

---

## 📦 Thresholds to Test

| Action               | Result                          | System Logic                           |
| :------------------- | :------------------------------ | :------------------------------------- |
| **1,000 NGN Spend**  | **"First Steps"** Achievement   | `PurchaseMade` -> `CheckAchievements`  |
| **10,000 NGN Spend** | **"Big Spender I"** Achievement | `TotalSpend >= 10,000`                 |
| **1 Achievement**    | **"Bronze Badge"**              | `AchievementUnlocked` -> `CheckBadges` |
| **2 Achievements**   | **"Gold Badge"**                | `Count >= 2`                           |

---

## ✅ Running Automated Tests

The project includes Feature tests covering the entire flow.

```bash
php artisan test
```

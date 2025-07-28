# 🚀 AI-First BI Tool: System Architecture & Laravel Scaffold

This document defines the initial system architecture and Laravel scaffolding plan for building a modern, AI-first BI tool designed for ResRequest clients, with a future path to general SaaS.

---

## 🔧 System Architecture Overview

```mermaid
graph TD
    A[Data Sources] -->|MariaDB, Sheets, Excel| B[Airbyte]
    B --> C[dbt: Transformations]
    C --> D[DuckDB/ClickHouse: Analytics DB]
    D --> E[Laravel API Layer]
    E --> F[Vue + Inertia Front-End]
    E --> G[AI Layer: NLQ + LLMs (OpenAI/Claude)]
    F --> H[ECharts/ChartJS Dashboards]
    G --> I[Natural Language Queries + Voice (Future)]
    E --> J[Semantic Layer via dbt Models]
    C --> J
```

---

## 🧱 Laravel Boilerplate Scaffold (MVP1)

### 1. **Core Structure**

Use the Laravel VILT stack:
- **Laravel 11**
- **InertiaJS**
- **Vue 3 + TailwindCSS**
- **Jetstream or Fortify** for multi-tenant auth (Jetstream preferred for Vue)

```bash
laravel new bi-platform --jet --inertia
cd bi-platform
php artisan migrate
```

---

### 2. **Multi-Tenant Support**

Use [Stancl/tenancy](https://tenancyforlaravel.com/) (per-database or per-schema):

```bash
composer require stancl/tenancy
php artisan tenancy:install
```

Edit `config/tenancy.php`:
```php
'database' => [
    'central_connection' => 'mysql',
    'tenant_connection' => 'tenant',
],
```

Create `TenantServiceProvider` to register scoped bindings, like a `ReportService` that adapts to the tenant DB.

---

### 3. **Airbyte + dbt Integration**

#### a. **Run Airbyte in Docker**
```bash
git clone https://github.com/airbytehq/airbyte.git
cd airbyte
./run-ab-platform.sh
```
Configure MariaDB source + DuckDB/ClickHouse destination.

#### b. **Add dbt**
```bash
pip install dbt-core dbt-duckdb dbt-mysql
```
Create `dbt_project` folder in Laravel root with:
```bash
mkdir dbt && cd dbt
dbt init bi_project
```
Add models in `dbt/models`, e.g.:
```sql
-- bookings.sql
SELECT id, created_at, amount, property_id FROM bookings
```

Run with:
```bash
dbt run
```

---

### 4. **AI NLQ Integration (OpenAI)**

Use OpenAI's SDK via Laravel:
```bash
composer require openai-php/client
```
Add an `AIService`:
```php
use OpenAI\Laravel\Facades\OpenAI;

class AIService {
  public function ask($question) {
    return OpenAI::completions()->create([
      'model' => 'gpt-4',
      'prompt' => "Translate this question to SQL: $question",
      ...
    ]);
  }
}
```

---

### 5. **Dashboard Components**

Use [Vue-ECharts](https://github.com/ecomfe/vue-echarts) or Chart.js

```bash
npm install echarts vue-echarts
```
Example:
```vue
<template>
  <v-chart :option="chartOptions" style="height: 400px" />
</template>

<script setup>
import { ref } from 'vue'
import VChart from 'vue-echarts'
import 'echarts'

const chartOptions = ref({
  title: { text: 'Revenue by Month' },
  xAxis: { type: 'category', data: ['Jan', 'Feb'] },
  yAxis: { type: 'value' },
  series: [{ data: [120, 200], type: 'bar' }]
})
</script>
```

---

### 6. **Project Structure (Suggested)**
```
bi-platform/
├── app/
│   ├── Http/Controllers/API/ReportController.php
│   ├── Services/AiService.php
│   └── Models/TenantAware Models
├── dbt/
│   └── models/
│       └── bookings.sql
├── resources/js/
│   └── Pages/Dashboards/
│       └── BookingDashboard.vue
├── public/
├── routes/
│   └── web.php
│   └── tenant.php
├── .env
├── docker/
│   └── airbyte-compose.yml
└── composer.json
```

---

## 🧪 Seed Data for MVP

You can extract data from ResRequest (MariaDB) and import into Airbyte as the first pipeline source. Use dummy or anonymized data for development/testing. Example tables:
- `bookings`
- `contacts`
- `payments`
- `properties`

---

## 🧠 Next Steps

1. Stand up Laravel + InertiaJS scaffold
2. Add Stancl tenancy
3. Run Airbyte locally to connect to a MariaDB dump
4. Build a sample dbt model (e.g. monthly revenue)
5. Render the first dashboard in Vue
6. Add a simple AI question box that hits OpenAI and returns text

Let me know when you're ready for the MVP timeline or a working code starter. You can also request individual Docker setup, dbt configs, or OpenAI prompts.
# AI-First BI Platform

A modern business intelligence platform built with Laravel, Vue.js, and AI capabilities.

## Quick Start

### Prerequisites
- PHP 8.2+
- Node.js 22+
- Docker & Docker Compose
- Composer

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd qlik-clone
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Start Docker services**
   ```bash
   docker-compose up -d
   ```

5. **Update .env with Docker services**
   ```bash
   # Copy the contents from .env.docker to your .env file
   # This includes PostgreSQL, Redis, and other service configurations
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Start development servers**
   ```bash
   # In one terminal
   npm run dev

   # In another terminal
   php artisan serve
   ```

Visit http://localhost:8000 to see the application.

## Docker Services

The application uses several Docker services:

- **PostgreSQL**: Main database (port 5432)
- **Redis**: Cache and queue backend (port 6379)
- **DuckDB**: Analytics database (embedded)
- **MinIO**: S3-compatible file storage (ports 9000/9001)
- **Airbyte**: Data pipeline (commented out, uncomment when ready)

## Features

- 📊 **Interactive Dashboards** - Create and manage data visualizations
- 🤖 **AI-Powered Insights** - Natural language queries for your data
- 🔄 **Data Pipeline** - Connect multiple data sources with Airbyte
- 📈 **Rich Visualizations** - Charts powered by ECharts
- 👥 **Multi-tenancy Ready** - Built for SaaS deployment
- 🌓 **Dark Mode** - Full theme support

## Development

### Code Quality
```bash
# PHP linting
./vendor/bin/pint

# JavaScript linting
npm run lint

# Format code
npm run format
```

### Testing
```bash
# Run PHP tests
php artisan test

# Run with coverage
php artisan test --coverage
```

## Architecture

See `docs/ARCHITECTURE.md` for detailed system architecture and design decisions.

## License

This project is proprietary software.
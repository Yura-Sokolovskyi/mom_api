# Mini eShop Order API

This project provides a simple Symfony-based Order Service API with Docker, Swagger UI, and database migrations.

## 🚀 Getting Started

### 1. Clone the Repository
```bash
git clone <your-repo-url>
cd <your-repo-folder>
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Configure Environment
Copy the default environment file:
```bash
cp .env .env.local
```
Update `.env.local` with your local database settings:
```
DB_NAME=symfony
DB_USER=symfony
DB_PASSWORD=symfony
```

### 4. Run Docker Containers
```bash
docker-compose up -d
```
> App will be available at: [http://localhost:8000](http://localhost:8000)

### 5. Run Migrations
Run the following command (if needed inside container):
```bash
php bin/console doctrine:migrations:migrate
```
If you get a permission error, run it inside the container:
```bash
docker exec -it mom_app bash
php bin/console doctrine:migrations:migrate
```

### 6. Access API Documentation
Visit:
```
http://localhost:8000/api/doc
```
To see interactive Swagger documentation powered by NelmioApiDocBundle.



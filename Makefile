setup:
	@if [ ! -f .env ]; then cp .env.example .env && echo ".env created from .env.example"; fi

up: setup
	docker compose up -d
	docker compose exec backend composer install
	$(MAKE) migrate

down:
	docker compose down

restart:
	docker compose restart

build: setup
	docker compose up -d --build
	docker compose exec backend composer install
	$(MAKE) migrate

logs:
	docker compose logs -f

ps:
	docker compose ps

migrate:
	docker compose exec -T postgres psql -U $$(grep DB_USER .env | cut -d= -f2) $$(grep DB_NAME .env | cut -d= -f2) < backend/src/database/migrations/001_create_jobs_table.sql

shell-backend:
	docker compose exec backend sh

shell-db:
	docker compose exec postgres psql -U $$(grep DB_USER .env | cut -d '=' -f2) $$(grep DB_NAME .env | cut -d '=' -f2)

composer:
	docker compose exec backend composer install

clean:
	docker compose down -v --remove-orphans

.PHONY: setup up down restart build logs ps migrate shell-backend shell-db composer clean
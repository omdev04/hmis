# ====================================================
# InfyHMS - Makefile (Easy Docker Commands)
# Usage: make <command>
# ====================================================

.PHONY: help build up down restart logs shell db-shell migrate seed fresh status clean nuke

# Colors
GREEN  := \033[0;32m
YELLOW := \033[1;33m
CYAN   := \033[0;36m
RESET  := \033[0m

help: ## 📖 Show this help
	@echo ""
	@echo "$(CYAN)🏥 InfyHMS Docker Commands$(RESET)"
	@echo "$(CYAN)══════════════════════════════$(RESET)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-20s$(RESET) %s\n", $$1, $$2}'
	@echo ""

# ── Setup ─────────────────────────────────────────
setup: ## 🚀 First time setup (build + start + migrate)
	@echo "$(CYAN)Setting up InfyHMS...$(RESET)"
	cp -n .docker.env .env || true
	docker compose build --no-cache
	docker compose up -d
	@echo "$(GREEN)✅ InfyHMS is running at http://localhost:8080$(RESET)"

build: ## 🔨 Build Docker images
	docker compose build

up: ## ▶️  Start all services
	docker compose up -d
	@echo "$(GREEN)✅ Started! App: http://localhost:8080 | PMA: http://localhost:8081$(RESET)"

down: ## ⏹️  Stop all services
	docker compose down

restart: ## 🔄 Restart all services
	docker compose restart

# ── Logs ──────────────────────────────────────────
logs: ## 📋 View all logs (live)
	docker compose logs -f

logs-app: ## 📋 View app logs only
	docker compose logs -f app

logs-mysql: ## 📋 View MySQL logs
	docker compose logs -f mysql

logs-queue: ## 📋 View queue worker logs
	docker compose logs -f queue

# ── Shell Access ───────────────────────────────────
shell: ## 🐚 Enter app container (bash)
	docker compose exec app bash

db-shell: ## 🗄️  Enter MySQL shell
	docker compose exec mysql mysql -u hms_user -phms_secret hms

redis-shell: ## 🔴 Enter Redis CLI
	docker compose exec redis redis-cli

# ── Laravel Commands ───────────────────────────────
migrate: ## 🗄️  Run database migrations
	docker compose exec app php artisan migrate

migrate-fresh: ## 🗄️  Fresh migration with seeders (⚠️ DELETES ALL DATA)
	docker compose exec app php artisan migrate:fresh --seed

seed: ## 🌱 Run database seeders
	docker compose exec app php artisan db:seed

artisan: ## 🎨 Run artisan command (usage: make artisan CMD="route:list")
	docker compose exec app php artisan $(CMD)

cache-clear: ## 🧹 Clear all caches
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear

cache-optimize: ## ⚡ Optimize caches for production
	docker compose exec app php artisan config:cache
	docker compose exec app php artisan route:cache
	docker compose exec app php artisan view:cache

storage-link: ## 🔗 Create storage symlink
	docker compose exec app php artisan storage:link

# ── Status ────────────────────────────────────────
status: ## 📊 Show containers status
	docker compose ps

# ── Cleanup ───────────────────────────────────────
clean: ## 🧹 Remove containers and orphans
	docker compose down --remove-orphans

nuke: ## 💣 Remove EVERYTHING including volumes (⚠️ DELETES DATABASE!)
	docker compose down -v --remove-orphans
	docker system prune -f

# ── Backup ────────────────────────────────────────
backup-db: ## 💾 Backup MySQL database
	@mkdir -p backups
	docker compose exec mysql mysqldump -u root -proot_secret_change_me_123 hms > backups/hms_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "$(GREEN)✅ Database backed up to backups/ folder$(RESET)"

restore-db: ## ♻️  Restore MySQL database (usage: make restore-db FILE=backups/hms_xxx.sql)
	docker compose exec -T mysql mysql -u root -proot_secret_change_me_123 hms < $(FILE)
	@echo "$(GREEN)✅ Database restored from $(FILE)$(RESET)"

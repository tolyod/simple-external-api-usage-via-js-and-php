include .env

stop:
	docker-compose -f docker-compose.yml down
start:
	docker-compose -f docker-compose.yml up -d
status:
	docker-compose -f docker-compose.yml ps
logs:
	docker-compose -f docker-compose.yml logs --tail=10 -f
setup:
	mkdir mysql_data mysql_socks mysql_logs;chmod 777 mysql_data mysql_socks mysql_logs; mv env.example .env
mysql:
	docker-compose exec mysql mysql -p${MYSQL_ROOT_PASSWORD} videostats
restart: stop start

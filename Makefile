stop:
        docker-compose -f docker-compose.yml down
start:
        docker-compose -f docker-compose.yml up -d
status:
        docker-compose -f docker-compose.yml ps
logs:
        docker-compose -f docker-compose.yml logs --tail=10 -f
setup:
	mkdir mysql_data mysql_socks mysql_logs;chmod 777 mysql_data mysql_socks mysql_logs
restart: stop start

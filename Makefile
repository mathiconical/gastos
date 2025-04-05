.PHONY: run-php stop-php run-server

run-php:
	@if ! podman network ls --filter name=^appnet$$ --format '{{.Name}}' | grep -q appnet; then \
		podman network create appnet; \
	fi
	podman run -it --rm --name php \
		-v ./app:/usr/app/ \
		--network appnet \
		-w /usr/app/ \
		-e APP_ENV=local \
		-p 9000:9000 \
		--privileged \
		--user=root \
		--entrypoint /bin/sh \
		localhost/php8.4.4-fpm-alpine3.21

stop-php:
	@podman stop php 2>/dev/null || true
	@podman rm php 2>/dev/null || true

run-server:
	@if ! podman network ls --filter name=^appnet$$ --format '{{.Name}}' | grep -q appnet; then \
		podman network create appnet; \
	fi
	podman run -it --rm --name php \
		-v ./app:/usr/app/ \
		--network appnet \
		-w /usr/app/ \
		-e APP_ENV=local \
		-p 9000:9000 \
		--privileged \
		--user=root \
		--entrypoint /bin/sh \
		localhost/php8.4.4-fpm-alpine3.21 \
		-c "php artisan serve --host=0.0.0.0 --port=9000"

#!/usr/bin/env bash
# Start / stop the yoga WordPress preview server.
set -euo pipefail

BASE="/tmp/opencode/wpyoga"
WP="$BASE/wordpress"
PHP="$BASE/php"
PIDFILE="$BASE/server.pid"
PORT="${PORT:-8080}"

stop() {
	if [ -f "$PIDFILE" ]; then
		pid="$(cat "$PIDFILE")"
		if kill -0 "$pid" 2>/dev/null; then
			kill "$pid" 2>/dev/null || true
			sleep 1
		fi
		rm -f "$PIDFILE"
	fi
}

start() {
	stop
	cd "$WP"
	nohup "$PHP" -S "0.0.0.0:$PORT" -t . router.php > "$BASE/server.log" 2>&1 &
	echo $! > "$PIDFILE"
	sleep 2
	code="$(curl -s -o /dev/null -w '%{http_code}' "http://localhost:$PORT/")"
	echo "server pid $(cat "$PIDFILE") on http://localhost:$PORT/  -> HTTP $code"
}

case "${1:-start}" in
	start) start ;;
	stop) stop; echo "stopped" ;;
	restart) start ;;
	*) echo "usage: $0 {start|stop|restart}" >&2; exit 1 ;;
esac

#!/usr/bin/env sh
set -eu
root="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
check(){ grep -Fq "$2" "$root/$1" || { echo "FALLO: $1 no contiene $2" >&2; exit 1; }; }
check config/version.php "'version'=>'2.2.1'"
check manifest.json '"version": "2.2.1"'
check dashboardcliente/index.php 'dashboard-220.css'
check dashboardcliente/index.php 'dashboard-220.js'
check dashboardcliente/index.php 'agDashboardCustomCss'
check dashboardcliente/index.php 'CSS adicional del sitio'
check assets/dashboard-220.js 'CSS adicional del panel'
check assets/dashboard-220.js 'Restablecer CSS del panel'
check assets/dashboard-220.js 'ag-admin-gallery-item'
check assets/dashboard-220.js 'multiple hidden'
check assets/dashboard-220.css '.ag-admin-gallery-editor'
check assets/dashboard-220.css 'grid-template-columns:minmax(180px,220px) minmax(0,1fr)'
check database/default-site.php "'dashboardCss'=>''"
if grep -Fq 'dashboardCss' "$root/index.php"; then echo 'FALLO: CSS del panel alcanzó el sitio público' >&2; exit 1; fi
node --check "$root/assets/dashboard-220.js"
echo 'OK: aislamiento y regresión estática Arandu Go 2.2.1'

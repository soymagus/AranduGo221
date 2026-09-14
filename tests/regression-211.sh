#!/usr/bin/env sh
set -eu
root="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
check(){ grep -Fq "$2" "$root/$1" || { echo "FALLO: $1 no contiene $2" >&2; exit 1; }; }
check config/version.php "'version'=>'2.2.1'"
check manifest.json '"version": "2.2.1"'
check dashboardcliente/index.php 'dashboard-211.css'
check dashboardcliente/index.php 'dashboard-211.js'
check assets/dashboard-211.js 'Salir del modo guiado'
check assets/dashboard-211.js 'Carga múltiple'
check assets/dashboard-211.js 'multiple hidden'
check assets/dashboard-211.js 'closeGalleryCards'
check assets/dashboard-211.js 'closeFreeCards'
check assets/dashboard-211.css 'grid-template-columns:68px minmax(0,1fr) auto'
node --check "$root/assets/dashboard-211.js"
echo 'OK: regresión estática Arandu Go 2.2.1'

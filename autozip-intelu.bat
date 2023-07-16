@echo on
set destination=C:\laragon\www\intelu\intelu.zip
"C:\Program Files\7-Zip\7z.exe" a -r -x!"C:\laragon\www\intelu\vendor" -x!"C:\laragon\www\intelu\storage" -x!"C:\laragon\www\intelu\node_modules"  %destination% "C:\laragon\www\intelu\app" "C:\laragon\www\intelu\resources" "C:\laragon\www\intelu\routes" "C:\laragon\www\intelu\database" "C:\laragon\www\intelu\public" "C:\laragon\www\intelu\lang"
pause
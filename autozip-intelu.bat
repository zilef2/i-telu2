@echo on
set destination=C:\laragon\www\proyectoGenerico\proyectoGenerico.zip
"C:\Program Files\7-Zip\7z.exe" a -r -x!"C:\laragon\www\proyectoGenerico\vendor" -x!"C:\laragon\www\proyectoGenerico\storage" -x!"C:\laragon\www\proyectoGenerico\node_modules"  %destination% "C:\laragon\www\proyectoGenerico\app" "C:\laragon\www\proyectoGenerico\resources" "C:\laragon\www\proyectoGenerico\routes" "C:\laragon\www\proyectoGenerico\database" "C:\laragon\www\proyectoGenerico\public" "C:\laragon\www\proyectoGenerico\lang"
pause
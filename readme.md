# Ordenes Compra

Proyecto para gestionar las ordenes de compra del departamento de finanzas

###Clone
Clonar con SSH

```
git clone git@github.schibsted.io:Yapo/ordenescompra.git
```

###Configurar BD
Crear y Modificar archivo .env
```
cp .env.example .env
```

###Actualizar composer
```
composer install
```

###Configurar Google Config
Configurar $URLSERVER en config/googleconfig.php

####Limpiar cache config (Consola)
```
$ php artisan config:clear
```

# Sistema de Notificaciones en Tiempo Real - BoxCenter

## Descripción
Se ha implementado un sistema de notificaciones en tiempo real usando **Laravel Reverb** (el servidor de websockets nativo de Laravel) y **Laravel Echo** en el frontend. Cuando un coach cancela una clase, todos los usuarios que tenían una reserva para esa clase reciben una notificación instantánea.

## 🎯 Características

- ✅ Notificaciones en tiempo real sin recargar la página
- ✅ Sistema de canales privados por usuario
- ✅ Notificaciones persistentes en base de datos
- ✅ Interfaz visual con animaciones suaves
- ✅ Auto-cierre después de 10 segundos
- ✅ Actualización automática de componentes Livewire
- ✅ Sistema completamente integrado con la autenticación

## Componentes Implementados

### Backend
1. **Evento: `App\Events\ClaseCancelada`**
   - Implementa `ShouldBroadcast` para transmitir en tiempo real
   - Envía notificaciones a canales privados de cada usuario afectado
   - Incluye información detallada de la clase cancelada

2. **Notificación: `App\Notifications\ClaseCanceladaNotification`**
   - Se guarda en la base de datos (tabla `notifications`)
   - Se transmite via broadcast a los usuarios
   - Incluye datos sobre la clase cancelada

3. **Lógica actualizada en `ViewClaseModal.php`**
   - Cuando el coach cancela una clase:
     - Obtiene todos los usuarios con reservas (`estado = 'reservo'`)
     - Envía notificaciones a cada usuario
     - Dispara el evento de broadcasting

4. **Configuración de Broadcasting**
   - Laravel Reverb configurado como driver de broadcasting
   - Canal privado `user.{id}` para cada usuario
   - Variables de entorno configuradas en `.env`
   - Ruta de autenticación `/broadcasting/auth`

### Frontend
1. **Laravel Echo configurado en `resources/js/bootstrap.js`**
   - Conecta con el servidor Reverb
   - Gestiona la autenticación de canales privados

2. **Componente Alpine.js en dashboard del atleta**
   - Escucha notificaciones en tiempo real
   - Muestra alertas visuales cuando se cancela una clase
   - Auto-oculta las notificaciones después de 10 segundos
   - Permite cerrar manualmente las notificaciones
   - Actualiza automáticamente los componentes Livewire

## 🚀 Cómo Usar

### Paso 1: Iniciar el Servidor de Reverb
Para que las notificaciones en tiempo real funcionen, debes iniciar el servidor de Reverb en una terminal separada:

```bash
php artisan reverb:start
```

Deberías ver:
```
INFO  Starting server on 0.0.0.0:8080 (localhost).
```

Este comando iniciará el servidor de websockets en `http://localhost:8080` (según la configuración del `.env`).

**⚠️ IMPORTANTE**: El servidor de Reverb debe estar corriendo TODO EL TIEMPO que quieras usar notificaciones en tiempo real.

### Paso 2: Iniciar la Aplicación
En otra terminal, inicia el servidor de desarrollo de Laravel:

```bash
php artisan serve
```

### Paso 3 (Opcional): Modo Desarrollo con Vite
Si estás trabajando en el frontend, también puedes ejecutar Vite en modo desarrollo en una tercera terminal:

```bash
npm run dev
```

Si prefieres compilar los assets una sola vez:

```bash
npm run build
```

## 📋 Flujo de Funcionamiento

1. **Coach cancela clase**: 
   - El coach abre el modal de una clase programada
   - Hace clic en el botón "Cancelar clase"

2. **Backend procesa**:
   - Se actualiza el estado de la clase a `cancelada`
   - Se obtienen todos los usuarios con reservas activas (`estado = 'reservo'`)
   - Se envía una notificación a cada usuario usando `Notification::send()`
   - Se dispara el evento `ClaseCancelada` con broadcasting

3. **Broadcasting transmite**: 
   - Laravel Reverb recibe el evento
   - Transmite a los canales privados `user.{id}` de cada usuario afectado

4. **Frontend recibe**: 
   - El dashboard del atleta (si está abierto) está suscrito al canal `user.{userId}`
   - Laravel Echo recibe el evento `.clase.cancelada`

5. **Usuario ve notificación**: 
   - Aparece un banner rojo animado en la esquina superior derecha
   - Muestra el mensaje con detalles de la clase cancelada

6. **Actualización automática**: 
   - Se dispara el evento `reserva-actualizada` de Livewire
   - Los componentes se actualizan sin recargar la página

## ⚙️ Variables de Entorno Configuradas

En tu archivo `.env`:

```env
# Broadcasting
BROADCAST_CONNECTION=reverb

# Reverb Server
REVERB_APP_ID=boxcenter
REVERB_APP_KEY=local-key
REVERB_APP_SECRET=local-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Variables para Vite (frontend)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## 📦 Dependencias Instaladas

### PHP (Composer)
```bash
composer require pusher/pusher-php-server
composer require laravel/reverb
```

### JavaScript (NPM)
```bash
npm install --save laravel-echo pusher-js
```

## 📁 Archivos Creados/Modificados

### ✨ Creados
- `app/Events/ClaseCancelada.php` - Evento de broadcasting
- `app/Notifications/ClaseCanceladaNotification.php` - Notificación persistente
- `database/migrations/XXXX_create_notifications_table.php` - Tabla de notificaciones
- `NOTIFICACIONES_BROADCASTING.md` - Esta documentación

### ✏️ Modificados
- `app/Livewire/Coach/ViewClaseModal.php` - Agregada lógica de notificaciones en `toggleEstadoClase()`
- `resources/js/bootstrap.js` - Configuración de Laravel Echo + Reverb
- `resources/views/athlete/dashboard.blade.php` - Componente Alpine.js para notificaciones
- `.env` - Variables de configuración de Reverb
- `routes/channels.php` - Canal privado `user.{id}`
- `routes/web.php` - Agregadas rutas de autenticación de broadcasting
- `package.json` - Nuevas dependencias (laravel-echo, pusher-js)

## 🧪 Pruebas

Para probar el sistema completo:

### 1. Preparación
```bash
# Terminal 1: Servidor Reverb
php artisan reverb:start

# Terminal 2: Servidor Laravel
php artisan serve

# Terminal 3 (opcional): Vite
npm run dev
```

### 2. Escenario de Prueba
1. Abre dos navegadores o ventanas (puede ser una normal y otra en modo incógnito)
2. **Ventana 1 - Coach:**
   - Inicia sesión como coach
   - Ve al calendario de clases
3. **Ventana 2 - Atleta:**
   - Inicia sesión como atleta (usuario normal)
   - Ve al dashboard
   - Reserva un lugar en una clase programada
4. **Vuelve a Ventana 1 - Coach:**
   - Encuentra la clase donde el atleta hizo la reserva
   - Haz clic en la clase para abrir el modal
   - Haz clic en "Cancelar clase"
5. **Observa en Ventana 2 - Atleta:**
   - ¡Deberías ver aparecer inmediatamente una notificación roja en la esquina superior derecha!
   - La notificación muestra los detalles de la clase cancelada
   - El banner de "Próxima clase" se actualiza automáticamente
   - La lista de clases disponibles también se actualiza

### 3. Verificar Logs
Si hay problemas, revisa:

**Backend (Laravel):**
```bash
tail -f storage/logs/laravel.log
```

**Frontend (Consola del navegador):**
- Abre DevTools (F12)
- Ve a la pestaña Console
- Deberías ver mensajes como: `Notificación recibida: {...}`

**Servidor Reverb:**
La terminal donde corre `php artisan reverb:start` muestra las conexiones y mensajes

## 🔍 Debugging

### El servidor no inicia
```bash
# Verifica que el puerto 8080 esté libre
netstat -ano | findstr :8080

# Si está ocupado, cambia el puerto en .env:
REVERB_PORT=8081
```

### Las notificaciones no llegan
1. ✅ Verifica que Reverb esté corriendo: `php artisan reverb:start`
2. ✅ Verifica la consola del navegador (F12) - ¿hay errores de conexión?
3. ✅ Verifica que los assets estén compilados: `npm run build`
4. ✅ Limpia la caché: `php artisan config:clear && php artisan cache:clear`
5. ✅ Verifica que el usuario tenga reserva en la clase (`estado = 'reservo'`)

### Error de autenticación en canales
- Asegúrate de que el usuario esté autenticado
- Verifica que la ruta `/broadcasting/auth` esté funcionando
- Revisa `routes/channels.php` y confirma el canal `user.{id}`

## 📝 Notas Importantes

- ⚠️ El servidor de Reverb **debe estar corriendo** para notificaciones en tiempo real
- 💾 Si Reverb no está corriendo, las notificaciones se guardan en BD pero no se transmiten
- 🔒 Los canales son **privados** y requieren autenticación
- 🌐 Configuración actual es para **desarrollo local** (HTTP)
- 🚀 Para **producción**, configura HTTPS y ajusta las variables de entorno
- 📊 Las notificaciones se almacenan en `notifications` table y pueden consultarse después
- 🔄 El sistema actualiza automáticamente los componentes Livewire cuando se recibe una notificación

## 🎨 Personalización

### Cambiar el tiempo de auto-cierre
En `resources/views/athlete/dashboard.blade.php`, línea ~110:
```javascript
setTimeout(() => {
    this.removeNotification(id);
}, 10000); // Cambia 10000 (10 segundos) al valor deseado en milisegundos
```

### Cambiar el estilo de la notificación
En `resources/views/athlete/dashboard.blade.php`, busca la clase `bg-red-500` y personaliza:
```html
<div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg max-w-md">
```

### Agregar sonido
Agrega en el método `addNotification`:
```javascript
addNotification(mensaje) {
    const audio = new Audio('/sounds/notification.mp3');
    audio.play();
    // ... resto del código
}
```

## 🔗 Referencias

- [Laravel Broadcasting](https://laravel.com/docs/11.x/broadcasting)
- [Laravel Reverb](https://reverb.laravel.com/)
- [Laravel Echo](https://laravel.com/docs/11.x/broadcasting#client-side-installation)
- [Laravel Notifications](https://laravel.com/docs/11.x/notifications)


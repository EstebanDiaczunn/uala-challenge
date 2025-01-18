# Twitter-like uala-challenge

## Arquitectura de Alto Nivel

### Visión General
Este proyecto implementa una plataforma de microblogging siguiendo los principios de Domain-Driven Design (DDD) con vertical slicing. La arquitectura está diseñada para escalar a millones de usuarios, con especial énfasis y obvia necesidad en la optimización de lecturas.

### Bounded Contexts
La aplicación está dividida en tres contextos principales:

1. User Context
   - Maneja la gestión de usuarios y relaciones de follow/following
   - Responsable de la lógica de negocio relacionada con las conexiones entre usuarios
   - Podria manejar tambien el modulo de sign in/up

2. Tweet Context
   - Gestiona la creación y almacenamiento de tweets
   - Implementa las reglas de negocio ej: (límite de 280 caracteres)

3. Timeline Context
   - Administra la generación y mantenimiento de timelines personalizados
   - Optimizado para lecturas rápidas y eficientes

### Stack Tecnológico

#### Backend Framework
- Laravel 10.x

#### Bases de Datos
1. PostgreSQL
   - Almacenamiento principal para usuarios y relaciones fuertes
   - Elegido por su consistencia y capacidad para manejar relaciones complejas
   
2. Redis
   - Cache de timelines y datos frecuentemente accedidos
   - Optimización de lecturas y reducción de carga en PostgreSQL

#### Message Broker
- RabbitMQ para comunicación asíncrona entre servicios
- Manejo eficiente de la distribución de tweets a timelines

#### Documentación API
- Scribe para generación automática de documentación
- Facilita el mantenimiento y prueba de endpoints

#### Swagger/OpenAPI
- Integrado para documentar la API con una interfaz más profesional y atractiva.
- Accede a la documentación en formato Swagger UI en: `http://tu-dominio/api/documentation`.
- Configurado con anotaciones OpenAPI en los controladores para generar automáticamente documentación interactiva.

### Patrones y Decisiones de Diseño

#### Arquitectura
- DDD con vertical slicing para mejor organización y mantenibilidad
- Cada contexto acotado (bounded context) mantiene su propia lógica de negocio

#### Autenticación
- Sistema simplificado basado en user_id via header
- Sin gestión de sesiones

#### Escalabilidad
- Diseño orientado a microservicios para facilitar escalado horizontal
- Uso de cache distribuido para optimizar lecturas
- Procesamiento asíncrono para tareas pesadas

### Estructura de Carpetas
```plaintext
App/
  Contexts/
    User/
      Application/
        Commands/
        Queries/
      Domain/
        Models/
        Repositories/
        Services/
      Infrastructure/
        Persistence/
        Http/
```


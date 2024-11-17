
# TPE - TERCER ENTREGA

## Ramiro Gogorza - Roman Gogorza

gogorzaramiro@gmail.com - romangogorza2003@gmail.com



# API de Jugadores ⚽  

Bienvenido a la documentación de la API para la gestión de jugadores. Aca encontrarás todas las funcionalidades disponibles para interactuar con nuestra base de datos de jugadores.  

---

## **Listar jugadores** 📋  

### **Endpoint:**  
`GET http://localhost/tp3/api/jugadores`  

### **Descripción:**  
Obtén una lista de todos los jugadores en la base de datos. Por defecto, los resultados se ordenan ascendentemente por nombre.  

### **Parámetros:**  
- **Ordenamiento:**  
  - `?campo=`: Cualquier columna de la base de datos por la cual ordenar (por ejemplo: `edad`). Si la columna no existe, se notificará.  
  - `&orden=`: Define el orden, puede ser `"ASC"` (ascendente) o `"DESC"` (descendente). Si no se especifica, el valor por defecto es `"ASC"`.  

- **Filtrado:**  
  Filtra los jugadores por nacionalidad.  
  - `?nacionalidad=`: Especifica la nacionalidad (por ejemplo: `Argentina`). Si no se encuentran jugadores de la nacionalidad indicada, se devolverá un arreglo vacío.  

- **Paginado:**  
  - `?pagina=`: Número de página a visualizar (debe ser mayor a 1).  
  - `&limite=`: Cantidad de jugadores a mostrar por página.  
  > **Nota:** Si falta el valor de página o límite, se notificará al usuario.  

---

## **Jugador específico** 🔍  

### **Endpoint:**  
`GET http://localhost/tp3/api/jugadores/ID`  

### **Descripción:**  
Obtén los datos de un jugador específico utilizando su **ID**.  

---

## **Eliminar Jugador** 🗑  

### **Endpoint:**  
`DELETE http://localhost/tp3/api/jugadores/ID`  

### **Descripción:**  
Elimina un jugador identificado por su **ID**. Si el jugador no existe, se notificará al usuario.  

---

## **Agregar Jugador** ➕  

### **Endpoint:**  
`POST http://localhost/tp3/api/jugadores`  

### **Descripción:**  
Agrega un nuevo jugador a la base de datos.  

### **Requisitos:**  
- Se debe obtener un **token** utilizando las credenciales:  
  - **Username:** `webadmin`  
  - **Password:** `admin`  
- Los datos del jugador deben enviarse en formato **JSON**.  
- El **ID** del jugador se generará automáticamente.  

---

## **Editar Jugador** ✏️  

### **Endpoint:**  
`PUT http://localhost/tp3/api/jugadores/ID`  

### **Descripción:**  
Edita la información de un jugador existente utilizando su **ID**.  

### **Requisitos:**  
- Autenticación mediante token (credenciales: `webadmin` / `admin`).  
- Enviar los datos en formato **JSON**.  

### **Ejemplo de solicitud:**  
```json
{
    "nombre": "Walter Bou",
    "nacionalidad": "Argentina",
    "posicion": "Delantero",
    "edad": 30,
    "id_club": 3
}
```  

---

/**
 *    File        : frontend/js/api/apiFactory.js
 *    Project     : CRUD PHP
 *    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
 *    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
 *    Date        : Mayo 2025
 *    Status      : Prototype
 *    Iteration   : 3.0 ( prototype )
 */

export function createAPI(moduleName, config = {}) {
  const API_URL =
    config.urlOverride ?? `../../backend/server.php?module=${moduleName}`;

  async function sendJSON(method, data) {
    const res = await fetch(API_URL, {
      method,
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });

    console.log("Respuesta del servidor:", res.status, res.statusText);

    if (!res.ok) {
      // Intentar obtener el mensaje real del error del servidor
      const errorData = await res.json();
      try {
        console.log("Error del servidor:", errorData);
      } catch (e) {
        console.log("No se pudo leer el error del servidor");
      }

      // Extraer el mensaje del objeto de error
      const errorMessage =
        errorData.error ||
        errorData.message ||
        `Error ${res.status}: ${res.statusText}`;
      throw new Error(errorMessage);
    }
    return await res.json();
  }

  return {
    async fetchAll() {
      const res = await fetch(API_URL);
      if (!res.ok) throw new Error("No se pudieron obtener los datos");
      return await res.json();
    },
    async create(data) {
      return await sendJSON("POST", data);
    },
    async update(data) {
      return await sendJSON("PUT", data);
    },
    async remove(id) {
      return await sendJSON("DELETE", { id });
    },
  };
}

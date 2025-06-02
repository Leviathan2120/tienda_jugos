// Función para validar campos vacíos
function validarCampoVacio(campo, mensajeError) {
    const valor = document.getElementById(campo).value.trim();
    if (valor === "") {
        mostrarMensajeError(campo, mensajeError);
        return false;
    }
    ocultarMensajeError(campo);
    return true;
}

// Función para validar correos electrónicos
function validarCorreo(campo) {
    const valor = document.getElementById(campo).value.trim();
    const regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regexCorreo.test(valor)) {
        mostrarMensajeError(campo, "Por favor, ingresa un correo válido.");
        return false;
    }
    ocultarMensajeError(campo);
    return true;
}

// Función para validar contraseñas seguras
function validarContrasena(campo) {
    const valor = document.getElementById(campo).value.trim();
    const regexContrasena = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/; // Mínimo 8 caracteres, al menos una letra y un número
    if (!regexContrasena.test(valor)) {
        mostrarMensajeError(campo, "La contraseña debe tener al menos 8 caracteres, incluyendo una letra y un número.");
        return false;
    }
    ocultarMensajeError(campo);
    return true;
}

// Validar número de teléfono
function validarTelefono(campo) {
    const valor = document.getElementById(campo).value.trim();
    const regexTelefono = /^\d{10}$/; // Acepta solo 10 dígitos
    if (!regexTelefono.test(valor)) {
        mostrarMensajeError(campo, "Por favor, ingresa un número de teléfono válido (10 dígitos).");
        return false;
    }
    ocultarMensajeError(campo);
    return true;
}

// Validar selección de dropdown
function validarSeleccion(campo, mensajeError) {
    const valor = document.getElementById(campo).value;
    if (valor === "" || valor === "default") {
        mostrarMensajeError(campo, mensajeError);
        return false;
    }
    ocultarMensajeError(campo);
    return true;
}

// Función para mostrar mensajes de error dinámicos
function mostrarMensajeError(campo, mensaje) {
    let mensajeError = document.getElementById(`${campo}-error`);
    if (!mensajeError) {
        mensajeError = document.createElement("span");
        mensajeError.id = `${campo}-error`;
        mensajeError.style.color = "red";
        document.getElementById(campo).parentNode.appendChild(mensajeError);
    }
    mensajeError.textContent = mensaje;
}

// Función para ocultar mensajes de error
function ocultarMensajeError(campo) {
    const mensajeError = document.getElementById(`${campo}-error`);
    if (mensajeError) {
        mensajeError.textContent = "";
    }
}

// Agregar contenido dinámico al DOM
function agregarElementoDinamico(contenedorId, contenido) {
    const contenedor = document.getElementById(contenedorId);
    const nuevoElemento = document.createElement("div");
    nuevoElemento.innerHTML = contenido;
    contenedor.appendChild(nuevoElemento);
}

// Elimina el ejemplo de uso directo para hacerlo modular
// Puedes importar y usar estas funciones en cualquier formulario de tu proyecto
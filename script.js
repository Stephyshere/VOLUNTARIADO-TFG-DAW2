// ==========================================================
// 1. INICIALIZACIÓN Y GESTIÓN DE LA BASE DE DATOS DE USUARIOS
// ==========================================================

// Base de datos de usuarios (simulada con localStorage)
const defaultUsers = {
    // Cuenta de Administrador (admin@mazarron.es / admin)
    'admin@mazarron.es': { password: 'admin', role: 'admin' }, 
    // Cuenta de Voluntario/Usuario Estándar
    'user@ejemplo.es': { password: 'user', role: 'voluntario' } 
};

/**
 * **CORRECCIÓN CLAVE:** Inicializa usersDB si no existe en localStorage.
 * Esto asegura que la cuenta de admin esté siempre disponible la primera vez.
 */
if (!localStorage.getItem('usersDB')) {
    localStorage.setItem('usersDB', JSON.stringify(defaultUsers));
}

let usersDB = JSON.parse(localStorage.getItem('usersDB'));
let currentUser = JSON.parse(sessionStorage.getItem('currentUser'));

// ==========================================================
// 2. FUNCIONES DE SESIÓN Y REDIRECCIÓN
// ==========================================================

/**
 * Verifica si el usuario está logeado y actualiza la interfaz de usuario.
 */
function checkLoginStatus() {
    const loginLink = document.getElementById('login-link');
    const logoutBtn = document.getElementById('logout-btn');
    const registerLink = document.getElementById('register-link');
    const adminLink = document.getElementById('admin-link');
    const loggedInUserText = document.getElementById('logged-in-user');

    const sessionUser = JSON.parse(sessionStorage.getItem('currentUser'));

    if (sessionUser) {
        // Usuario logeado
        if (loginLink) loginLink.style.display = 'none';
        if (registerLink) registerLink.style.display = 'none';
        if (logoutBtn) logoutBtn.style.display = 'block';
        
        if (loggedInUserText) {
             loggedInUserText.textContent = `Bienvenido, ${sessionUser.role}`;
             loggedInUserText.style.display = 'inline';
        }

        // Mostrar enlace de Admin solo si el rol es 'admin'
        if (adminLink) {
            adminLink.style.display = (sessionUser.role === 'admin') ? 'block' : 'none';
        }
    } else {
        // Usuario deslogeado
        if (loginLink) loginLink.style.display = 'block';
        if (registerLink) registerLink.style.display = 'block';
        if (logoutBtn) logoutBtn.style.display = 'none';
        if (adminLink) adminLink.style.display = 'none';
        if (loggedInUserText) loggedInUserText.style.display = 'none';
    }
}

/**
 * Maneja el cierre de sesión.
 */
function handleLogout() {
    sessionStorage.removeItem('currentUser');
    window.location.href = 'index.html'; // Redireccionar a la página principal
}

// ==========================================================
// 3. LÓGICA DE FORMULARIOS
// ==========================================================

// --- MANEJO DEL LOGIN ---
const loginForm = document.getElementById('login-form');
if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;

        // Cargar usersDB por si fue modificado
        const currentUsersDB = JSON.parse(localStorage.getItem('usersDB'));
        
        // **VALIDACIÓN DE CREDENCIALES CORREGIDA**
        if (currentUsersDB[email] && currentUsersDB[email].password === password) {
            
            const user = currentUsersDB[email]; // Obtener datos del usuario
            
            // Establecer la sesión en sessionStorage
            sessionStorage.setItem('currentUser', JSON.stringify({ email: email, role: user.role }));
            
            alert('¡Inicio de sesión exitoso!');
            
            // Redireccionar según el rol
            if (user.role === 'admin') {
                window.location.href = 'admin.html';
            } else {
                window.location.href = 'index.html';
            }
        } else {
            alert('❌ Email o contraseña incorrectos.');
        }
    });
}

// --- MANEJO DEL REGISTRO (para referencia) ---
const registerForm = document.getElementById('register-form');
if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('register-email').value;
        const password = document.getElementById('register-password').value;
        const confirmPassword = document.getElementById('register-confirm-password').value;

        if (password !== confirmPassword) {
            alert('Las contraseñas no coinciden.');
            return;
        }

        const currentUsersDB = JSON.parse(localStorage.getItem('usersDB'));

        if (currentUsersDB[email]) {
            alert('Este email ya está registrado.');
            return;
        }

        // Añadir nuevo usuario como 'voluntario' por defecto
        currentUsersDB[email] = { password: password, role: 'voluntario' };
        localStorage.setItem('usersDB', JSON.stringify(currentUsersDB));
        
        alert('✅ Registro exitoso. Ahora puedes iniciar sesión.');
        window.location.href = 'login.html';
    });
}

// ==========================================================
// 4. EVENT LISTENERS GENERALES
// ==========================================================

// Listener para el botón de Logout
const logoutBtn = document.getElementById('logout-btn');
if (logoutBtn) {
    logoutBtn.addEventListener('click', handleLogout);
}

// Ejecutar al cargar la página para actualizar el estado de login en la cabecera
document.addEventListener('DOMContentLoaded', checkLoginStatus);

// ----------------------------------------------------------
// (Nota: Las funciones de la barra de navegación móvil 
// y el carrusel de proyectos deben ir después de esto si existen)
// ----------------------------------------------------------
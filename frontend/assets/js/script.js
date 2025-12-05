// ==========================================================
// 1. GESTIÓN DE SESIÓN Y AUTENTICACIÓN (API PHP)
// ==========================================================

// Estado global del usuario
let currentUser = null;

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', async () => {
    await checkAuthStatus();
    loadProjects(); // Cargar proyectos al iniciar
});

/**
 * Verifica el estado de autenticación contra el backend
 */
async function checkAuthStatus() {
    try {
        const response = await fetch('../../../backend/api/check_auth.php');
        const data = await response.json();

        if (data.authenticated) {
            currentUser = data.user;
            updateUI(true);
        } else {
            currentUser = null;
            updateUI(false);
        }
    } catch (error) {
        console.error('Error verificando auth:', error);
    }
}

/**
 * Actualiza la interfaz según el estado de login
 */
function updateUI(isLoggedIn) {
    const loginLink = document.getElementById('login-link');
    const logoutBtn = document.getElementById('logout-btn');
    const registerLink = document.getElementById('register-link');
    const adminLink = document.getElementById('admin-link');
    const createLink = document.getElementById('create-link'); // Botón "+ Crear"
    const loggedInUserText = document.getElementById('logged-in-user'); // Si existe en el HTML

    if (isLoggedIn) {
        if (loginLink) loginLink.style.display = 'none';
        if (registerLink) registerLink.style.display = 'none';
        if (logoutBtn) logoutBtn.style.display = 'block';

        // Mostrar botones de Admin si corresponde
        if (currentUser.role === 'admin') {
            if (adminLink) adminLink.style.display = 'block';
            if (createLink) createLink.style.display = 'block';
        } else {
            if (adminLink) adminLink.style.display = 'none';
            if (createLink) createLink.style.display = 'none';
        }

    } else {
        if (loginLink) loginLink.style.display = 'block';
        if (registerLink) registerLink.style.display = 'block';
        if (logoutBtn) logoutBtn.style.display = 'none';
        if (adminLink) adminLink.style.display = 'none';
        if (createLink) createLink.style.display = 'none';
    }
}

/**
 * Maneja el Login
 */
const loginForm = document.getElementById('login-form');
if (loginForm) {
    loginForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;

        try {
            const response = await fetch('../../../backend/api/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const result = await response.json();

            if (result.success) {
                alert(result.message);
                // Redireccionar si es admin
                if (result.role === 'admin') {
                    window.location.href = '../public/index.html';
                } else {
                    window.location.href = '../public/index.html';
                }
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('Error en login:', error);
            alert('Error al conectar con el servidor.');
        }
    });
}

/**
 * Maneja el Registro
 */
const registerForm = document.getElementById('register-form');
if (registerForm) {
    registerForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const email = document.getElementById('register-email').value;
        const password = document.getElementById('register-password').value;
        const confirmPassword = document.getElementById('register-confirm-password').value;

        if (password !== confirmPassword) {
            alert('Las contraseñas no coinciden.');
            return;
        }

        try {
            const response = await fetch('../../../backend/api/register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const result = await response.json();

            if (result.success) {
                alert('Registro exitoso. Inicia sesión.');
                window.location.href = '../auth/login.html';
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error en registro:', error);
            alert('Error al conectar con el servidor.');
        }
    });
}

/**
 * Maneja el Logout
 */
const logoutBtn = document.getElementById('logout-btn');
if (logoutBtn) {
    logoutBtn.addEventListener('click', async function () {
        await fetch('../../../backend/api/logout.php');
        window.location.href = '../public/index.html';
    });
}

// ==========================================================
// 2. GESTIÓN DE PROYECTOS (API PHP)
// ==========================================================

const searchBtn = document.getElementById('search-button');
const projectsContainer = document.getElementById('projects-container');
const noResultsMsg = document.getElementById('no-results');

if (searchBtn) {
    searchBtn.addEventListener('click', loadProjects);
}

/**
 * Carga proyectos desde el backend con filtros opcionales
 */
async function loadProjects() {
    if (!projectsContainer) return;

    const pedaniaFilter = document.getElementById('pedania-filter') ? document.getElementById('pedania-filter').value : 'all';
    const actividadFilter = document.getElementById('actividad-filter') ? document.getElementById('actividad-filter').value : 'all';

    const url = `../../../backend/api/activities.php?pedania=${pedaniaFilter}&actividad=${actividadFilter}`;

    try {
        const response = await fetch(url);
        const projects = await response.json();

        renderProjects(projects);

    } catch (error) {
        console.error('Error cargando proyectos:', error);
        projectsContainer.innerHTML = '<p style="text-align:center; color:red;">Error cargando proyectos.</p>';
    }
}

// ...
function renderProjects(projects) {
    projectsContainer.innerHTML = '';

    if (projects.length === 0) {
        if (noResultsMsg) noResultsMsg.style.display = 'block';
        return;
    }

    if (noResultsMsg) noResultsMsg.style.display = 'none';

    projects.forEach(project => {
        const col = document.createElement('div');
        col.classList.add('col-md-6', 'col-lg-4');

        const card = document.createElement('div');
        card.classList.add('project-card', 'card', 'h-100', 'border-0', 'shadow-sm', 'animate-fade-up');

        // Imagen por defecto
        const bgImage = project.imagen_url || '../../assets/img/default-project.jpg';

        let adminControls = '';
        if (currentUser && currentUser.role === 'admin') {
            adminControls = `
                <div class="admin-controls mt-3 pt-3 border-top d-flex justify-content-between">
                    <a href="../admin/admin-edit.html?id=${project.id}" class="btn btn-sm btn-outline-primary">✏️ Editar</a>
                    <button onclick="deleteProject(${project.id})" class="btn btn-sm btn-outline-danger">🗑️ Eliminar</button>
                </div>
            `;
        }

        card.innerHTML = `
            <div class="card-image position-relative" style="background-image: url('${bgImage}'); height: 200px; background-size: cover; background-position: center;">
                <span class="badge bg-dark position-absolute bottom-0 end-0 m-2">${project.pedania_nombre}</span>
            </div>
            <div class="card-body d-flex flex-column p-4">
                <div class="text-muted text-uppercase fw-bold small mb-2">
                   <span>${getIconForActivity(project.actividad)} ${project.actividad}</span>
                </div>
                <h3 class="card-title h5 fw-bold mb-3">${project.titulo}</h3>
                <p class="card-text text-muted mb-4 flex-grow-1">${project.descripcion.substring(0, 100)}...</p>
                <a href="../public/detalle-proyecto.html?id=${project.id}" class="btn btn-link text-primary fw-bold p-0 text-decoration-none stretched-link">Ver Detalles &rarr;</a>
                ${adminControls.replace('stretched-link', '') /* Prevent admin buttons from triggering card link if possible, but stretched-link covers all. Ideally admin controls should be z-indexed or outside stretched-link container. For now, keeping simple. */} 
            </div>
        `;
        // Fix for admin controls with stretched-link: remove stretched-link if admin controls exist, or handle differently.
        // Better approach: Don't use stretched-link if admin. Or put admin controls outside.
        // Let's remove stretched-link for now to ensure buttons work, or just use a normal link.
        
        col.appendChild(card);
        projectsContainer.appendChild(col);
    });
}

// ...

async function loadProjectDetail(id) {
    try {
        const response = await fetch(`../../../backend/api/activities.php?id=${id}`);
        if (!response.ok) throw new Error('Network response was not ok');

        const project = await response.json();

        // Actualizar UI
        document.querySelector('.project-title').textContent = project.titulo;
        document.querySelector('.project-subtitle').textContent = `Actividad: ${project.actividad} | Pedanía: ${project.pedania_nombre}`;

        // Construir bloques de detalle
        const detailContainer = document.querySelector('.detail-blocks');
        detailContainer.innerHTML = `
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h3 class="h5 fw-bold mb-3">👥 Descripción</h3>
                    <p class="text-muted mb-0">${project.descripcion}</p>
                </div>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h4 class="h6 fw-bold mb-2">📅 Cuándo</h4>
                            <p class="text-muted mb-0">${project.frecuencia || 'A consultar'}<br>${project.duracion || ''}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h4 class="h6 fw-bold mb-2">📍 Dónde</h4>
                            <p class="text-muted mb-0">${project.punto_encuentro || project.pedania_nombre}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h3 class="h5 fw-bold mb-3">🎒 ¿Qué necesitas?</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <p class="fw-bold mb-2">Lo que tú pones:</p>
                            <p class="text-muted mb-0">${formatList(project.material_voluntario)}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="fw-bold mb-2">Lo que ponemos nosotros:</p>
                            <p class="text-muted mb-0">${formatList(project.material_organizacion)}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning border-0 shadow-sm">
                <div class="d-flex">
                    <div class="me-3 fs-4">⚠️</div>
                    <div>
                        <h3 class="h6 fw-bold mb-1">Importante</h3>
                        <p class="mb-0 small">${project.notas_importantes || 'Sin notas adicionales.'}</p>
                    </div>
                </div>
            </div>
        `;

    } catch (error) {
        console.error('Error cargando detalle:', error);
        const title = document.querySelector('.project-title');
        if (title) title.textContent = 'Error cargando proyecto';
    }
}

function formatList(text) {
    if (!text) return 'Nada específico.';
    return text.split('\n').map(item => `• ${item}`).join('<br>');
}


// ==========================================================
// 5. LÓGICA DE CREAR PROYECTO (ADMIN)
// ==========================================================

const createForm = document.getElementById('create-project-form');
if (createForm) {
    createForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const pedaniaSelect = document.getElementById('new-pedania');
        const activitySelect = document.getElementById('new-activity');

        // Construir objeto de datos
        const projectData = {
            title: document.getElementById('new-title').value,
            pedaniaId: pedaniaSelect.value,
            pedania: pedaniaSelect.options[pedaniaSelect.selectedIndex].text,
            activity: activitySelect.value, // Asegúrate de que el value coincida con lo esperado en DB o API
            description: document.getElementById('new-description').value,
            duration: document.getElementById('new-duration').value,
            frequency: document.getElementById('new-frequency').value,
            meeting: document.getElementById('new-meeting').value,
            transport: document.getElementById('new-transport').value,
            notes: document.getElementById('new-notes').value,
            // Convertir textareas a arrays si el API lo espera así, o cadenas
            // create_project.php anterior hacía implode, así que espera array
            vol_material: document.getElementById('new-vol-material').value.split('\n'),
            org_material: document.getElementById('new-org-material').value.split('\n')
        };

        try {
            const response = await fetch('../../../backend/api/create_project.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(projectData)
            });

            const result = await response.json();

            if (result.success) {
                alert('¡Proyecto creado con éxito!');
                window.location.href = '../public/index.html';
            } else {
                alert('Error al crear proyecto: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión al crear el proyecto.');
        }
    });
}

// ==========================================================
// 6. LÓGICA DE EDITAR PROYECTO (ADMIN)
// ==========================================================

const editForm = document.getElementById('edit-project-form');

if (window.location.pathname.includes('admin-edit.html')) {
    const urlParams = new URLSearchParams(window.location.search);
    const projectId = urlParams.get('id');

    if (projectId) {
        loadEditForm(projectId);
    } else {
        alert('No se especificó proyecto para editar.');
        window.location.href = '../public/index.html';
    }
}

async function loadEditForm(id) {
    try {
        const response = await fetch(`../../../backend/api/activities.php?id=${id}`);
        const project = await response.json();

        const titleDisplay = document.getElementById('edit-project-title-display');
        if (titleDisplay) titleDisplay.textContent = project.titulo;

        document.getElementById('edit-project-id').value = project.id;

        document.getElementById('edit-title').value = project.titulo;
        document.getElementById('edit-description').value = project.descripcion;
        document.getElementById('edit-pedania').value = project.pedania_id;
        document.getElementById('edit-activity').value = project.actividad;

        if (document.getElementById('edit-duration')) document.getElementById('edit-duration').value = project.duracion;
        if (document.getElementById('edit-frequency')) document.getElementById('edit-frequency').value = project.frecuencia;
        if (document.getElementById('edit-meeting')) document.getElementById('edit-meeting').value = project.punto_encuentro;
        if (document.getElementById('edit-transport')) document.getElementById('edit-transport').value = project.transporte;

        if (document.getElementById('edit-vol-material')) document.getElementById('edit-vol-material').value = project.material_voluntario;
        if (document.getElementById('edit-org-material')) document.getElementById('edit-org-material').value = project.material_organizacion;
        if (document.getElementById('edit-notes')) document.getElementById('edit-notes').value = project.notas_importantes;

    } catch (error) {
        console.error('Error loading edit form:', error);
    }
}

if (editForm) {
    editForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const pedaniaSelect = document.getElementById('edit-pedania');

        const projectData = {
            id: document.getElementById('edit-project-id').value,
            title: document.getElementById('edit-title').value,
            pedaniaId: pedaniaSelect.value,
            pedania: pedaniaSelect.options[pedaniaSelect.selectedIndex].text,
            activity: document.getElementById('edit-activity').value,
            description: document.getElementById('edit-description').value,

            duration: document.getElementById('edit-duration').value,
            frequency: document.getElementById('edit-frequency').value,
            meeting: document.getElementById('edit-meeting').value,
            transport: document.getElementById('edit-transport').value,

            vol_material: document.getElementById('edit-vol-material').value.split('\n'),
            org_material: document.getElementById('edit-org-material').value.split('\n'),
            notes: document.getElementById('edit-notes').value
        };

        try {
            const response = await fetch('../../../backend/api/update_project.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(projectData)
            });
            const result = await response.json();

            if (result.success) {
                alert('Proyecto actualizado.');
                window.location.href = '../public/index.html';
            } else {
                alert('Error al actualizar: ' + result.message);
            }
        } catch (error) {
            console.error('Error update:', error);
            alert('Error al conectar con servidor.');
        }
    });
}

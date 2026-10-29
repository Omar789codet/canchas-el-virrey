const API = '/api';
let token = localStorage.getItem('token');
let usuarioActual = null;
const app = document.getElementById('app');
const headerTitle = document.querySelector('header h2');

if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/pwa/service-worker.js');
}

async function api(path, options = {}) {
  const res = await fetch(API + path, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...(options.headers || {}),
    },
  });
  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw data;
  return data;
}

function render(html) { app.innerHTML = html; }

function opcionesHora() {
  let html = '';
  for (let h = 0; h < 24; h++) {
    for (const m of [0, 30]) {
      const valor = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
      const hora12 = h % 12 === 0 ? 12 : h % 12;
      const ampm = h < 12 ? 'AM' : 'PM';
      const etiqueta = `${hora12}:${String(m).padStart(2, '0')} ${ampm}`;
      html += `<option value="${valor}">${etiqueta}</option>`;
    }
  }
  return html;
}

async function cerrarSesion() {
  await api('/auth/logout', { method: 'POST' }).catch(() => {});
  localStorage.removeItem('token');
  token = null;
  usuarioActual = null;
  headerTitle.textContent = 'Canchas El Virrey';
  vistaLogin();
}

// ---------- Panel de perfil (arriba a la derecha) ----------
function renderTopbarPerfil() {
  if (!usuarioActual) return '';
  const inicial = (usuarioActual.name || '?').trim().charAt(0).toUpperCase();
  return `
    <div class="topbar-user" id="topbarUser">
      <span class="avatar-circle">${inicial}</span>
      <span class="topbar-name">${usuarioActual.name}<small>${usuarioActual.email}</small></span>
      <span class="topbar-chevron">▾</span>
      <div class="topbar-menu" id="topbarMenu">
        <button id="btnVerPerfil">Editar mi perfil</button>
        <button id="btnCerrarSesion" class="danger">Cerrar sesión</button>
      </div>
    </div>
  `;
}

function activarTopbarPerfil(vistaAlVolver) {
  const toggle = document.getElementById('topbarUser');
  const menu = document.getElementById('topbarMenu');
  if (!toggle) return;

  toggle.addEventListener('click', (e) => {
    e.stopPropagation();
    menu.classList.toggle('open');
  });
  document.addEventListener('click', () => menu.classList.remove('open'), { once: true });

  document.getElementById('btnVerPerfil').onclick = () => vistaPerfil(vistaAlVolver);
  document.getElementById('btnCerrarSesion').onclick = cerrarSesion;
}

function actualizarHeader() {
  headerTitle.textContent = usuarioActual ? `Hola, ${usuarioActual.name.split(' ')[0]}` : 'Canchas El Virrey';
  const slot = document.getElementById('headerPerfilSlot');
  if (slot) slot.innerHTML = renderTopbarPerfil();
  activarTopbarPerfil('canchas');
}

// ---------- Login / Registro ----------
function vistaLogin() {
  headerTitle.textContent = 'Canchas El Virrey';
  const slot = document.getElementById('headerPerfilSlot');
  if (slot) slot.innerHTML = '';

  render(`
    <div class="card">
      <h3>Iniciar sesión</h3>
      <input id="email" type="email" placeholder="Correo">
      <input id="password" type="password" placeholder="Contraseña">
      <button id="btnLogin">Ingresar</button>
    </div>
    <div class="card">
      <h3>Registrarme</h3>
      <input id="rname" placeholder="Nombre completo">
      <input id="remail" type="email" placeholder="Correo">
      <input id="rtelefono" placeholder="Teléfono">
      <input id="rpassword" type="password" placeholder="Contraseña">
      <button id="btnRegister">Crear cuenta</button>
    </div>
    <p id="msg"></p>
  `);

  document.getElementById('btnLogin').onclick = async () => {
    try {
      const data = await api('/auth/login', {
        method: 'POST',
        body: JSON.stringify({
          email: document.getElementById('email').value,
          password: document.getElementById('password').value,
        }),
      });
      token = data.token;
      usuarioActual = data.user;
      localStorage.setItem('token', token);
      vistaCanchas();
    } catch (e) {
      document.getElementById('msg').textContent = e.message || 'Error al iniciar sesión.';
    }
  };

  document.getElementById('btnRegister').onclick = async () => {
    try {
      const data = await api('/auth/register', {
        method: 'POST',
        body: JSON.stringify({
          name: document.getElementById('rname').value,
          email: document.getElementById('remail').value,
          telefono: document.getElementById('rtelefono').value,
          password: document.getElementById('rpassword').value,
        }),
      });
      token = data.token;
      usuarioActual = data.user;
      localStorage.setItem('token', token);
      vistaCanchas();
    } catch (e) {
      document.getElementById('msg').textContent = 'No se pudo registrar. Revisa los datos.';
    }
  };
}

// ---------- Canchas y reserva ----------
async function vistaCanchas() {
  const canchas = await api('/canchas');
  render(`
    <div class="tabs">
      <button class="active">Reservar</button>
      <button id="irMisReservas">Mis reservas</button>
    </div>
    <div class="card">
      <h3>Reservar cancha</h3>
      <select id="cancha_id">
        ${canchas.map((c) => `<option value="${c.id}" data-precio="${c.precio_hora}">${c.nombre} (Bs. ${c.precio_hora}/h)</option>`).join('')}
      </select>
      <input type="date" id="fecha">
      <label style="font-size:.85rem;color:var(--text-muted)">Hora inicio</label>
      <select id="hora_inicio">${opcionesHora()}</select>
      <label style="font-size:.85rem;color:var(--text-muted)">Hora fin</label>
      <select id="hora_fin">${opcionesHora()}</select>
      <button id="btnReservar">Reservar</button>
      <p id="msg"></p>
    </div>
  `);
  actualizarHeader();

  document.getElementById('irMisReservas').onclick = vistaMisReservas;

  document.getElementById('btnReservar').onclick = async () => {
    try {
      await api('/reservas', {
        method: 'POST',
        body: JSON.stringify({
          cancha_id: document.getElementById('cancha_id').value,
          fecha: document.getElementById('fecha').value,
          hora_inicio: document.getElementById('hora_inicio').value,
          hora_fin: document.getElementById('hora_fin').value,
        }),
      });
      vistaMisReservas();
    } catch (e) {
      document.getElementById('msg').textContent = e.errors
        ? Object.values(e.errors).flat().join(' ')
        : 'No se pudo crear la reserva.';
    }
  };
}

async function vistaMisReservas() {
  const reservas = await api('/mis-reservas');
  render(`
    <div class="tabs">
      <button id="irCanchas">Reservar</button>
      <button class="active">Mis reservas</button>
    </div>
    ${reservas.map((r) => `
      <div class="card">
        <strong>${r.cancha.nombre}</strong><br>
        ${r.fecha} · ${r.hora_inicio.slice(0,5)} - ${r.hora_fin.slice(0,5)}<br>
        Pago: <span class="badge ${r.pago ? r.pago.estado : 'pendiente'}">${r.pago ? r.pago.estado : 'pendiente'}</span>
      </div>
    `).join('') || '<p>Todavía no tienes reservas.</p>'}
  `);
  actualizarHeader();
  document.getElementById('irCanchas').onclick = vistaCanchas;
}

// ---------- Editar perfil ----------
function vistaPerfil(vistaAlVolver) {
  render(`
    <div class="card">
      <h3>Mi perfil</h3>
      <input id="pname" placeholder="Nombre completo" value="${usuarioActual.name}">
      <input id="pemail" type="email" placeholder="Correo" value="${usuarioActual.email}">
      <input id="ptelefono" placeholder="Teléfono" value="${usuarioActual.telefono || ''}">
      <input id="ppassword" type="password" placeholder="Nueva contraseña (opcional)">
      <button id="btnGuardarPerfil">Guardar cambios</button>
      <button id="btnVolverPerfil" style="background:var(--surface-2);color:var(--text);margin-top:.5rem;">Volver</button>
      <p id="msg"></p>
    </div>
  `);

  document.getElementById('btnVolverPerfil').onclick = () =>
    vistaAlVolver === 'misReservas' ? vistaMisReservas() : vistaCanchas();

  document.getElementById('btnGuardarPerfil').onclick = async () => {
    try {
      const data = await api('/profile', {
        method: 'PUT',
        body: JSON.stringify({
          name: document.getElementById('pname').value,
          email: document.getElementById('pemail').value,
          telefono: document.getElementById('ptelefono').value,
          password: document.getElementById('ppassword').value || null,
        }),
      });
      usuarioActual = data.user;
      vistaCanchas();
    } catch (e) {
      document.getElementById('msg').textContent = e.errors
        ? Object.values(e.errors).flat().join(' ')
        : 'No se pudo actualizar el perfil.';
    }
  };
}

// ---------- Arranque ----------
async function iniciar() {
  if (!token) return vistaLogin();
  try {
    usuarioActual = await api('/auth/me');
    vistaCanchas();
  } catch (e) {
    localStorage.removeItem('token');
    token = null;
    vistaLogin();
  }
}
iniciar();

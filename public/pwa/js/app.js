const API = '/api';
let token = localStorage.getItem('token');
const app = document.getElementById('app');

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

// ---------- Horarios en formato 12h (AM/PM) para los <select> ----------
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

// ---------- Login / Registro ----------
function vistaLogin() {
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
      <button id="cerrarSesion">Salir</button>
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

  document.getElementById('irMisReservas').onclick = vistaMisReservas;
  document.getElementById('cerrarSesion').onclick = async () => {
    await api('/auth/logout', { method: 'POST' }).catch(() => {});
    localStorage.removeItem('token');
    token = null;
    vistaLogin();
  };

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
      <button id="cerrarSesion">Salir</button>
    </div>
    ${reservas.map((r) => `
      <div class="card">
        <strong>${r.cancha.nombre}</strong><br>
        ${r.fecha} · ${r.hora_inicio.slice(0,5)} - ${r.hora_fin.slice(0,5)}<br>
        Pago: <span class="badge ${r.pago ? r.pago.estado : 'pendiente'}">${r.pago ? r.pago.estado : 'pendiente'}</span>
      </div>
    `).join('') || '<p>Todavía no tienes reservas.</p>'}
  `);
  document.getElementById('irCanchas').onclick = vistaCanchas;
  document.getElementById('cerrarSesion').onclick = async () => {
    await api('/auth/logout', { method: 'POST' }).catch(() => {});
    localStorage.removeItem('token');
    token = null;
    vistaLogin();
  };
}

// ---------- Arranque ----------
if (token) {
  vistaCanchas().catch(() => { localStorage.removeItem('token'); vistaLogin(); });
} else {
  vistaLogin();
}

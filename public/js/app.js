// =====================================================================
// Gestão de Compras e Estoque — comportamentos compartilhados (Fase 5)
// =====================================================================

document.addEventListener('DOMContentLoaded', function () {

  // ---------- Sidebar (mobile) ----------
  const toggleBtn = document.getElementById('gc-sidebar-toggle');
  const sidebar = document.querySelector('.gc-sidebar');
  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', () => sidebar.classList.toggle('show'));
    document.addEventListener('click', (e) => {
      if (window.innerWidth < 992 && sidebar.classList.contains('show')
          && !sidebar.contains(e.target) && e.target !== toggleBtn) {
        sidebar.classList.remove('show');
      }
    });
  }

  // ---------- Toasts de flash message (sucesso / erro) ----------
  document.querySelectorAll('.toast').forEach((toastEl) => {
    const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
    toast.show();
  });

  // ---------- Modal de confirmação genérico ----------
  // Qualquer botão/link com data-confirm-delete abre o #gc-confirm-modal,
  // ajusta a mensagem e o action do form antes de exibir.
  const confirmModalEl = document.getElementById('gc-confirm-modal');
  if (confirmModalEl) {
    const confirmModal = new bootstrap.Modal(confirmModalEl);
    const confirmForm = confirmModalEl.querySelector('form');
    const confirmMessage = confirmModalEl.querySelector('[data-confirm-message]');
    const confirmTitle = confirmModalEl.querySelector('[data-confirm-title]');
    const confirmMethodField = confirmModalEl.querySelector('[name="_method"]');

    document.querySelectorAll('[data-confirm-delete]').forEach((trigger) => {
      trigger.addEventListener('click', (e) => {
        e.preventDefault();
        confirmForm.action = trigger.dataset.url || '#';
        confirmMessage.textContent = trigger.dataset.message || 'Tem certeza que deseja confirmar esta ação?';
        confirmTitle.textContent = trigger.dataset.title || 'Confirmar ação';
        if (confirmMethodField) {
          confirmMethodField.value = trigger.dataset.method || 'DELETE';
        }
        confirmModal.show();
      });
    });
  }

  // ---------- Marca o link ativo da sidebar pela URL atual ----------
  const currentPath = window.location.pathname;
  document.querySelectorAll('.gc-nav-link').forEach((link) => {
    const href = link.getAttribute('href');
    if (href && href !== '#' && currentPath.startsWith(href.replace(window.location.origin, ''))) {
      link.classList.add('active');
    }
  });

  // ---------- Inicializa tooltips do Bootstrap ----------
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
});

// =====================================================================
// Repetidor de itens — usado em compras/create.blade.php
// Disponível globalmente para ser chamado pela view.
// =====================================================================
function gcAdicionarItem(template, container) {
  const idx = container.querySelectorAll('.gc-item-row').length;
  const html = template.innerHTML.replaceAll('__INDEX__', idx);
  const wrapper = document.createElement('div');
  wrapper.innerHTML = html;
  container.appendChild(wrapper.firstElementChild);
}

function gcRemoverItem(btn) {
  const row = btn.closest('.gc-item-row');
  const container = row.parentElement;
  if (container.querySelectorAll('.gc-item-row').length > 1) {
    row.remove();
  }
}

function gcRecalcularTotalItem(row) {
  const qtd = parseFloat(row.querySelector('.gc-item-qtd')?.value || 0);
  const valorUnit = parseFloat(row.querySelector('.gc-item-valor')?.value || 0);
  const totalEl = row.querySelector('.gc-item-total');
  if (totalEl) {
    totalEl.textContent = (qtd * valorUnit).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  }
  gcRecalcularTotalGeral();
}

function gcRecalcularTotalGeral() {
  const totalGeralEl = document.getElementById('gc-total-geral');
  if (!totalGeralEl) return;
  let total = 0;
  document.querySelectorAll('.gc-item-row').forEach((row) => {
    const qtd = parseFloat(row.querySelector('.gc-item-qtd')?.value || 0);
    const valorUnit = parseFloat(row.querySelector('.gc-item-valor')?.value || 0);
    total += qtd * valorUnit;
  });
  totalGeralEl.textContent = total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

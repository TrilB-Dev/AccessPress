const accesspressProfileBuilder = () => {
  const root = document.querySelector('[data-profile-builder]');
  if (!root || root.dataset.initialized === 'true') {
    return;
  }

  root.dataset.initialized = 'true';

  const configInput = document.getElementById('accesspress-profile-layout');
  const tabHost = root.querySelector('[data-profile-tabs]');
  const addTabButton = root.querySelector('[data-profile-add-tab]');
  const libraryCards = root.querySelectorAll('.accesspress-profile-field-card');

  const readConfig = () => {
    try {
      const payload = root.dataset.profileConfig || '[]';
      const parsed = JSON.parse(payload);
      return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
      return [];
    }
  };

  const state = { tabs: readConfig() };

  const defaultField = (fieldType = 'text', group = 'custom') => ({
    id: `${group}-${fieldType}-${Date.now()}-${Math.random().toString(16).slice(2, 7)}`,
    type: fieldType,
    label: fieldType.charAt(0).toUpperCase() + fieldType.slice(1).replace(/[-_]/g, ' '),
    key: `${fieldType}-${Date.now()}`,
    placeholder: '',
    required: false,
    order: 0,
    group,
    options: [],
    meta: {},
  });

  const buildFieldMarkup = (field, index) => {
    const requiredLabel = field.required ? 'Required' : 'Optional';
    return `
      <div class="list-group-item accesspress-profile-field-item" draggable="true" data-field-id="${field.id}" data-field-index="${index}">
        <div class="d-flex justify-content-between align-items-center gap-2">
          <div>
            <strong class="d-block">${(field.label || 'Field').replace(/</g, '&lt;')}</strong>
            <small class="text-secondary">${(field.type || 'field').replace(/</g, '&lt;')} · ${requiredLabel}</small>
          </div>
          <button type="button" class="btn btn-link btn-sm text-danger p-0 accesspress-profile-delete-field" aria-label="Remove field">×</button>
        </div>
        <details class="mt-2 mb-0">
          <summary class="small text-secondary">${window.accesspress_profile_i18n?.configure || 'Configure'}</summary>
          <div class="mt-2">
            <div class="mb-2">
              <label class="form-label small">Label</label>
              <input type="text" class="form-control form-control-sm accesspress-profile-field-label" value="${(field.label || '').replace(/"/g, '&quot;')}" data-field-key="label" />
            </div>
            <div class="mb-2">
              <label class="form-label small">Placeholder</label>
              <input type="text" class="form-control form-control-sm accesspress-profile-field-placeholder" value="${(field.placeholder || '').replace(/"/g, '&quot;')}" data-field-key="placeholder" />
            </div>
            <div class="form-check">
              <input class="form-check-input accesspress-profile-field-required" type="checkbox" ${field.required ? 'checked' : ''} data-field-key="required" />
              <label class="form-check-label small">Required</label>
            </div>
          </div>
        </details>
      </div>
    `;
  };

  const buildTabMarkup = (tab, tabIndex) => {
    const fields = Array.isArray(tab.fields) ? tab.fields : [];
    return `
      <div class="card accesspress-profile-tab mb-3" data-tab-id="${tab.id || `tab-${tabIndex}`}" data-tab-index="${tabIndex}">
        <div class="card-header d-flex justify-content-between align-items-center gap-3">
          <div class="d-flex align-items-center gap-2 flex-grow-1">
            <span class="badge bg-primary-subtle text-primary">${tabIndex + 1}</span>
            <input type="text" class="form-control form-control-sm accesspress-profile-tab-label" value="${(tab.label || 'Tab').replace(/"/g, '&quot;')}" data-tab-label="${tab.id || `tab-${tabIndex}`}" />
          </div>
          <button type="button" class="btn btn-outline-danger btn-sm accesspress-profile-delete-tab" data-tab-delete="${tab.id || `tab-${tabIndex}`}">Delete</button>
        </div>
        <div class="card-body accesspress-profile-tab-body" data-tab-body="${tab.id || `tab-${tabIndex}`}">
          <div class="list-group accesspress-profile-field-list" data-field-list="${tab.id || `tab-${tabIndex}`}">
            ${fields.map((field, fieldIndex) => buildFieldMarkup(field, fieldIndex)).join('')}
          </div>
          <div class="mt-3 text-muted small">Drop a field card here to add it to this tab.</div>
        </div>
      </div>
    `;
  };

  const syncState = () => {
    state.tabs = state.tabs.map((tab, index) => ({
      ...tab,
      order: index,
      fields: Array.isArray(tab.fields) ? tab.fields.map((field, fieldIndex) => ({
        ...field,
        order: fieldIndex,
        required: Boolean(field.required),
      })) : [],
    }));

    configInput.value = JSON.stringify(state.tabs);
  };

  const renderTabs = () => {
    if (!tabHost) {
      return;
    }

    tabHost.innerHTML = state.tabs.length ? state.tabs.map(buildTabMarkup).join('') : '<div class="text-muted">No tabs yet.</div>';
    syncState();
  };

  const duplicateFieldAllowed = (fieldGroup, fieldType, tabIndex) => {
    const tab = state.tabs[tabIndex];
    if (!tab || !Array.isArray(tab.fields)) {
      return true;
    }

    if ('built_in' === fieldGroup) {
      return !tab.fields.some((field) => field.type === fieldType);
    }

    return true;
  };

  const addToTab = (tabIndex, fieldType, fieldGroup) => {
    if (!state.tabs[tabIndex]) {
      return;
    }

    if (!duplicateFieldAllowed(fieldGroup, fieldType, tabIndex)) {
      window.alert('This WordPress profile field can only be used once per tab.');
      return;
    }

    const field = defaultField(fieldType, fieldGroup);
    state.tabs[tabIndex].fields = Array.isArray(state.tabs[tabIndex].fields) ? state.tabs[tabIndex].fields : [];
    state.tabs[tabIndex].fields.push(field);
    renderTabs();
  };

  const deleteTab = (tabIndex) => {
    if (state.tabs.length <= 1) {
      state.tabs = [{ id: `tab-${Date.now()}`, label: 'Tab 1', layout: 'horizontal', order: 0, fields: [] }];
      renderTabs();
      return;
    }

    state.tabs.splice(tabIndex, 1);
    renderTabs();
  };

  const deleteField = (tabIndex, fieldIndex) => {
    if (!state.tabs[tabIndex] || !Array.isArray(state.tabs[tabIndex].fields)) {
      return;
    }

    state.tabs[tabIndex].fields.splice(fieldIndex, 1);
    renderTabs();
  };

  const attachFieldDropHandlers = () => {
    if (!tabHost) {
      return;
    }

    libraryCards.forEach((card) => {
      card.addEventListener('dragstart', (event) => {
        const payload = JSON.stringify({
          type: 'field-card',
          fieldType: card.dataset.fieldType,
          fieldGroup: card.dataset.fieldGroup,
        });
        event.dataTransfer.setData('application/accesspress-profile-field', payload);
        event.dataTransfer.effectAllowed = 'copy';
      });
    });

    tabHost.addEventListener('dragover', (event) => {
      const targetList = event.target.closest('.accesspress-profile-field-list');
      if (!targetList) {
        return;
      }
      event.preventDefault();
      targetList.classList.add('border-primary');
    });

    tabHost.addEventListener('dragleave', (event) => {
      const targetList = event.target.closest('.accesspress-profile-field-list');
      if (!targetList) {
        return;
      }
      targetList.classList.remove('border-primary');
    });

    tabHost.addEventListener('drop', (event) => {
      event.preventDefault();
      const targetList = event.target.closest('.accesspress-profile-field-list');
      const payloadText = event.dataTransfer.getData('application/accesspress-profile-field');
      const tabCard = event.target.closest('.accesspress-profile-tab');
      if (!targetList || !tabCard) {
        return;
      }

      try {
        const payload = JSON.parse(payloadText || '{}');
        const tabIndex = Number(tabCard.dataset.tabIndex);
        if (payload.fieldType) {
          addToTab(tabIndex, payload.fieldType, payload.fieldGroup || 'custom');
        }
      } catch (error) {
        // No-op.
      }

      targetList.classList.remove('border-primary');
    });
  };

  if (addTabButton) {
    addTabButton.addEventListener('click', () => {
      const nextIndex = state.tabs.length;
      state.tabs.push({
        id: `tab-${Date.now()}`,
        label: `Tab ${nextIndex + 1}`,
        layout: 'horizontal',
        order: nextIndex,
        fields: [],
      });
      renderTabs();
    });
  }

  tabHost.addEventListener('click', (event) => {
    const deleteTabButton = event.target.closest('[data-tab-delete]');
    if (deleteTabButton) {
      const index = Number(deleteTabButton.closest('.accesspress-profile-tab')?.dataset.tabIndex);
      deleteTab(index);
      return;
    }

    const deleteFieldButton = event.target.closest('.accesspress-profile-delete-field');
    if (deleteFieldButton) {
      const tab = deleteFieldButton.closest('.accesspress-profile-tab');
      const fieldItem = deleteFieldButton.closest('.accesspress-profile-field-item');
      const tabIndex = Number(tab?.dataset.tabIndex);
      const fieldIndex = Number(fieldItem?.dataset.fieldIndex);
      deleteField(tabIndex, fieldIndex);
      return;
    }
  });

  tabHost.addEventListener('input', (event) => {
    const tabLabelInput = event.target.closest('.accesspress-profile-tab-label');
    if (tabLabelInput) {
      const tab = tabLabelInput.closest('.accesspress-profile-tab');
      const tabIndex = Number(tab?.dataset.tabIndex);
      if (!Number.isNaN(tabIndex) && state.tabs[tabIndex]) {
        state.tabs[tabIndex].label = tabLabelInput.value || 'Untitled tab';
      }
      syncState();
      return;
    }

    const fieldLabelInput = event.target.closest('.accesspress-profile-field-label');
    const fieldPlaceholderInput = event.target.closest('.accesspress-profile-field-placeholder');
    const fieldRequiredInput = event.target.closest('.accesspress-profile-field-required');

    if (fieldLabelInput || fieldPlaceholderInput || fieldRequiredInput) {
      const tab = event.target.closest('.accesspress-profile-tab');
      const field = event.target.closest('.accesspress-profile-field-item');
      const tabIndex = Number(tab?.dataset.tabIndex);
      const fieldIndex = Number(field?.dataset.fieldIndex);
      if (!Number.isNaN(tabIndex) && !Number.isNaN(fieldIndex) && state.tabs[tabIndex]?.fields?.[fieldIndex]) {
        const targetField = state.tabs[tabIndex].fields[fieldIndex];
        if (fieldLabelInput) {
          targetField.label = fieldLabelInput.value || targetField.label;
        }
        if (fieldPlaceholderInput) {
          targetField.placeholder = fieldPlaceholderInput.value;
        }
        if (fieldRequiredInput) {
          targetField.required = fieldRequiredInput.checked;
        }
      }
      syncState();
    }
  });

  renderTabs();
  attachFieldDropHandlers();
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', accesspressProfileBuilder);
} else {
  accesspressProfileBuilder();
}

const accesspressRegistrationBuilder = () => {
  const root = document.querySelector('[data-registration-builder]');
  if (!root || root.dataset.initialized === 'true') {
    return;
  }

  root.dataset.initialized = 'true';

  const configInput = document.getElementById('accesspress-registration-layout');
  const pageHost = root.querySelector('[data-registration-pages]');
  const addPageButton = root.querySelector('[data-registration-add-page]');
  const libraryCards = root.querySelectorAll('.accesspress-registration-field-card');

  const readConfig = () => {
    try {
      const payload = root.dataset.registrationConfig || '[]';
      const parsed = JSON.parse(payload);
      return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
      return [];
    }
  };

  const state = { pages: readConfig() };

  const defaultField = (fieldType = 'text', group = 'custom') => ({
    id: `${group}-${fieldType}-${Date.now()}-${Math.random().toString(16).slice(2, 7)}`,
    type: fieldType,
    label: fieldType.charAt(0).toUpperCase() + fieldType.slice(1).replace(/[-_]/g, ' '),
    key: `${fieldType}-${Date.now()}`,
    placeholder: '',
    required: false,
    order: 0,
    group,
    visibility: 'user',
    editable: true,
    admin_only: false,
    options: [],
    meta: {},
  });

  const buildFieldMarkup = (field, index) => {
    const requiredLabel = field.required ? 'Required' : 'Optional';
    return `
      <div class="list-group-item accesspress-registration-field-item" draggable="true" data-field-id="${field.id}" data-field-index="${index}">
        <div class="d-flex justify-content-between align-items-center gap-2">
          <div>
            <strong class="d-block">${(field.label || 'Field').replace(/</g, '&lt;')}</strong>
            <small class="text-secondary">${(field.type || 'field').replace(/</g, '&lt;')} · ${requiredLabel}</small>
          </div>
          <button type="button" class="btn btn-link btn-sm text-danger p-0 accesspress-registration-delete-field" aria-label="Remove field">×</button>
        </div>
      </div>
    `;
  };

  const buildPageMarkup = (page, pageIndex) => {
    const pageFields = Array.isArray(page.fields) ? page.fields : [];
    const isDefault = Boolean(page.default_page || 0 === pageIndex);
    return `
      <div class="card accesspress-registration-page mb-3" data-page-id="${page.id || `page-${pageIndex}`}" data-page-index="${pageIndex}">
        <div class="card-header d-flex justify-content-between align-items-center gap-3">
          <div class="d-flex align-items-center gap-2 flex-grow-1">
            <span class="badge bg-primary-subtle text-primary">${pageIndex + 1}</span>
            <input type="text" class="form-control form-control-sm accesspress-registration-page-label" value="${(page.label || 'Page').replace(/"/g, '&quot;')}" data-page-label="${page.id || `page-${pageIndex}`}" ${isDefault ? 'readonly' : ''} />
          </div>
          <button type="button" class="btn btn-outline-danger btn-sm accesspress-registration-delete-page" data-page-delete="${page.id || `page-${pageIndex}`}" ${isDefault ? 'disabled' : ''}>Delete</button>
        </div>
        <div class="card-body accesspress-registration-page-body" data-page-body="${page.id || `page-${pageIndex}`}">
          <div class="list-group accesspress-registration-field-list" data-field-list="${page.id || `page-${pageIndex}`}">
            ${pageFields.map((field, fieldIndex) => buildFieldMarkup(field, fieldIndex)).join('')}
          </div>
          <div class="mt-3 text-muted small">Drop a field card here to add it to this page.</div>
        </div>
      </div>
    `;
  };

  const syncState = () => {
    state.pages = state.pages.map((page, index) => ({
      ...page,
      order: index,
      default_page: 0 === index,
      can_delete: 0 !== index,
      fields: Array.isArray(page.fields) ? page.fields.map((field, fieldIndex) => ({
        ...field,
        order: fieldIndex,
        required: Boolean(field.required),
      })) : [],
    }));

    configInput.value = JSON.stringify(state.pages);
  };

  const renderPages = () => {
    if (!pageHost) {
      return;
    }

    pageHost.innerHTML = state.pages.length ? state.pages.map(buildPageMarkup).join('') : '<div class="text-muted">No registration page yet.</div>';
    syncState();
  };

  const addToPage = (pageIndex, fieldType, fieldGroup) => {
    if (!state.pages[pageIndex]) {
      return;
    }

    const field = defaultField(fieldType, fieldGroup);
    state.pages[pageIndex].fields = Array.isArray(state.pages[pageIndex].fields) ? state.pages[pageIndex].fields : [];
    state.pages[pageIndex].fields.push(field);
    renderPages();
  };

  const deletePage = (pageIndex) => {
    if (state.pages.length <= 1 || (state.pages[pageIndex]?.default_page ?? false)) {
      return;
    }

    state.pages.splice(pageIndex, 1);
    renderPages();
  };

  const deleteField = (pageIndex, fieldIndex) => {
    if (!state.pages[pageIndex] || !Array.isArray(state.pages[pageIndex].fields)) {
      return;
    }

    state.pages[pageIndex].fields.splice(fieldIndex, 1);
    renderPages();
  };

  if (addPageButton) {
    addPageButton.addEventListener('click', () => {
      const nextIndex = state.pages.length;
      state.pages.push({
        id: `page-${Date.now()}`,
        label: `Page ${nextIndex + 1}`,
        layout: 'horizontal',
        order: nextIndex,
        default_page: false,
        can_delete: true,
        fields: [],
      });
      renderPages();
    });
  }

  libraryCards.forEach((card) => {
    card.addEventListener('dragstart', (event) => {
      const payload = JSON.stringify({
        type: 'field-card',
        fieldType: card.dataset.fieldType,
        fieldGroup: card.dataset.fieldGroup,
      });
      event.dataTransfer.setData('application/accesspress-registration-field', payload);
      event.dataTransfer.effectAllowed = 'copy';
    });
  });

  pageHost.addEventListener('dragover', (event) => {
    const targetList = event.target.closest('.accesspress-registration-field-list');
    if (!targetList) {
      return;
    }
    event.preventDefault();
    targetList.classList.add('border-primary');
  });

  pageHost.addEventListener('dragleave', (event) => {
    const targetList = event.target.closest('.accesspress-registration-field-list');
    if (!targetList) {
      return;
    }
    targetList.classList.remove('border-primary');
  });

  pageHost.addEventListener('drop', (event) => {
    event.preventDefault();
    const targetList = event.target.closest('.accesspress-registration-field-list');
    const pageCard = event.target.closest('.accesspress-registration-page');
    const payloadText = event.dataTransfer.getData('application/accesspress-registration-field');
    if (!targetList || !pageCard) {
      return;
    }

    try {
      const payload = JSON.parse(payloadText || '{}');
      const pageIndex = Number(pageCard.dataset.pageIndex);
      if (payload.fieldType) {
        addToPage(pageIndex, payload.fieldType, payload.fieldGroup || 'custom');
      }
    } catch (error) {
      // No-op.
    }

    targetList.classList.remove('border-primary');
  });

  pageHost.addEventListener('click', (event) => {
    const deletePageButton = event.target.closest('[data-page-delete]');
    if (deletePageButton) {
      const index = Number(deletePageButton.closest('.accesspress-registration-page')?.dataset.pageIndex);
      deletePage(index);
      return;
    }

    const deleteFieldButton = event.target.closest('.accesspress-registration-delete-field');
    if (deleteFieldButton) {
      const page = deleteFieldButton.closest('.accesspress-registration-page');
      const fieldItem = deleteFieldButton.closest('.accesspress-registration-field-item');
      const pageIndex = Number(page?.dataset.pageIndex);
      const fieldIndex = Number(fieldItem?.dataset.fieldIndex);
      deleteField(pageIndex, fieldIndex);
    }
  });

  pageHost.addEventListener('input', (event) => {
    const pageLabelInput = event.target.closest('.accesspress-registration-page-label');
    if (pageLabelInput) {
      const page = pageLabelInput.closest('.accesspress-registration-page');
      const pageIndex = Number(page?.dataset.pageIndex);
      if (!Number.isNaN(pageIndex) && state.pages[pageIndex]) {
        state.pages[pageIndex].label = pageLabelInput.value || 'Page';
      }
      syncState();
      return;
    }
  });

  renderPages();
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', accesspressRegistrationBuilder);
} else {
  accesspressRegistrationBuilder();
}

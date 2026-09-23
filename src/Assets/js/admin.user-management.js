const accesspressUserManagementInit = () => {
  const root = document.querySelector('[data-profile-builder]') || document;

  root.querySelectorAll('[data-bs-toggle="collapse"]').forEach((trigger) => {
    if (trigger.dataset.initialized === 'true') {
      return;
    }
    trigger.dataset.initialized = 'true';
  });
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', accesspressUserManagementInit);
} else {
  accesspressUserManagementInit();
}

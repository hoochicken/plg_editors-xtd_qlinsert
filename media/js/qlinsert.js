/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Runs inside the modal iframe. Sends the chosen snippet to the parent editor
 * via the Joomla 5/6 postMessage protocol; the core "modal" button action
 * inserts it and closes the dialog.
 */
document.addEventListener('click', (event) => {
  const button = event.target.closest('[data-qlinsert]');
  if (!button) {
    return;
  }
  event.preventDefault();
  const source = document.getElementById(button.dataset.qlinsert);
  window.parent.postMessage(
    { messageType: 'joomla:content-select', html: source ? source.value : '' },
    window.location.origin,
  );
});

document.addEventListener('DOMContentLoaded', () => {
  const PARAGRAPH_MODALS = document.querySelectorAll('[data-paragraph-modal]');

  const LOCALSTORAGE_UTILS: any = {
    /**
     * Sets a localstorage entry for when the modal should show again.
     *
     * @param {string} key - The unquie id of the localstorage entry.
     * @param {number} frequency - How long in seconds should the modal wait to show again.
     *                           - Can also be 0 for Immediate or -1 for never.
     */
    setModalTimeout: (key: string, frequency: number) => {
      const NOW = new Date();

      // Save -1 so the modal will never show again.
      if (frequency === -1) {
        localStorage.setItem(key, (-1).toString());
      }
      // Only save a local storage entry if the frequency is greater than 0.
      else if (frequency > 0) {
        localStorage.setItem(key, (NOW.getTime() + (frequency * 1000)).toString());
      }
    },
    /**
     * Returns true of false based on whether or not the time elapsed since
     * showing the modal. Always return false if the time is "-1".
     *
     * @param {string} key - The unquie id of the localstorage entry.
     *
     * @returns {boolean}
     */
    modalTimeoutExpired: (key: string) => {
      const NOW = new Date();

      let modalTimeout = localStorage.getItem(key);

      if (!modalTimeout) {
        return true;
      }
      else {
        // If the modalTimeout is -1, never show it again.
        if (modalTimeout === '-1') {
          return false;
        }
        // If the modalTimeout doesn't exist or the current time is greater.
        else if (NOW.getTime().toString() >= modalTimeout) {
          localStorage.removeItem(key);

          return true;
        }
      }
    }
  };

  const MODAL_SETUP: any = {
    /**
     * Initializes all the modal using trigger "scroll".
     *
     * @param {any} modal - The object containing all the info about the modal.
     */
    scroll: (modal: any) => {
      if (LOCALSTORAGE_UTILS.modalTimeoutExpired(modal.storageKey)) {
        const scroll = () => {
          let scrollHeight = document.body.scrollHeight;
          let innerHeight = window.innerHeight;
          let scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
          // Clamp value between 0 and 100
          let scrollPercentage = Math.round(Math.min(Math.max(0, (scrollTop / ( scrollHeight - innerHeight)) * 100), 100));

          if (scrollPercentage >= modal.settings.scroll_delay) {
            MODAL.show(modal);

            window.removeEventListener('scroll', scroll);
          }
        }

        window.addEventListener('scroll', scroll);
      }
    },
    /**
     * Initializes all the modal using trigger "manual".
     *
     * @param {any} modal - The object containing all the info about the modal.
     */
    manual: (modal: any) => {
      const MODAL_TRIGGER_BUTTONS = document.querySelectorAll(`[aria-controls="${ modal.id }"]`);

      MODAL_TRIGGER_BUTTONS.forEach((modalTriggerButton: Element) => {
        modalTriggerButton.addEventListener('click', () => {
          MODAL.show(modal);
        });
      });
    },
    /**
     * Initializes all the modal using trigger "timer".
     *
     * @param {any} modal - The object containing all the info about the modal.
     */
    timer: (modal: any) => {
      setTimeout(() => {
        MODAL.show(modal);
      }, modal.settings.timer_delay * 1000);
    }
  };

  const MODAL: any = {
    show: (modal: any) => {
      LOCALSTORAGE_UTILS.setModalTimeout(modal.storageKey, modal.settings.frequency);

      const BANISH_MODAL_BUTTON = modal.element.querySelector('[class$="banish-button"]');
      const CLOSE_MODAL_BUTTON = modal.element.querySelector('[class$="close-button"]');

      // Make the modal visible.
      modal.element.setAttribute('aria-hidden', 'false');
      modal.element.classList.remove('stcm-modal--hidden');

      // The modal will never show again.
      if (BANISH_MODAL_BUTTON) {
        BANISH_MODAL_BUTTON.addEventListener('click', () => {
          LOCALSTORAGE_UTILS.setModalTimeout(modal.storageKey, -1);
          MODAL.hide(modal);
        });
      }

      // Hide the modal.
      if (CLOSE_MODAL_BUTTON) {
        CLOSE_MODAL_BUTTON.addEventListener('click', () => {
          MODAL.hide(modal);
        });
      }
    },

    hide: (modal: any) => {
      modal.element.setAttribute('aria-hidden', 'true');
      modal.element.classList.add('stcm-modal--hidden');
    }
  }

  // **************************************************
  // SETUP EACH MODAL

  PARAGRAPH_MODALS.forEach((paragraphModal: Element) => {
    let modal: any = {
      id: paragraphModal.getAttribute('id'),
      element: paragraphModal,
      settings: JSON.parse(paragraphModal.getAttribute('data-modal-settings') || '{}'),
      storageKey: `modal.${ paragraphModal.getAttribute('id') }`,
    };

    // Dynamically call function from MODAL_SETUP using the "modal.settings.trigger".
    MODAL_SETUP[modal.settings.trigger](modal);
  });
});

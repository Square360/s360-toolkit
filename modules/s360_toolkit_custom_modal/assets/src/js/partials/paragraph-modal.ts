interface IModalSettings {
  trigger: string;
  scroll_delay?: number;
  timer_delay?: number;
  frequency: number;
  position: string;
};

interface IModal {
  id: string | null;
  element: HTMLElement;
  settings: IModalSettings;
  storageKey: string;
}

/**
 * Sets a localstorage entry for when the modal should show again.
 *
 * @param {string} key - The unquie id of the localstorage entry.
 * @param {number} frequency - How long in seconds should the modal wait to show again.
 *                           - Can also be 0 for Immediate or -1 for never.
 */
function setModalTimeout(key: string, frequency: number) {
  const NOW = new Date();

  // Save -1 so the modal will never show again.
  if (frequency === -1) {
    localStorage.setItem(key, (-1).toString());
  }
  // Only save a local storage entry if the frequency is greater than 0.
  else if (frequency > 0) {
    localStorage.setItem(key, (NOW.getTime() + (frequency * 1000)).toString());
  }
}

/**
 * Returns true of false based on whether or not the time elapsed since
 * showing the modal. Always return false if the time is "-1".
 *
 * @param {string} key - The unquie id of the localstorage entry.
 *
 * @returns {boolean}
 */
function modalTimeoutExpired(key: string) {
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
class Modal {
  public id: string | null;
  public element: HTMLElement;
  public settings: IModalSettings;
  public storageKey: string;

  private _banishButton: HTMLElement | null;
  private _closeButton: HTMLElement | null;
  private _firstFocusableElement: HTMLElement | null;
  private _lastFocusableElement: HTMLElement | null;
  private _lastFocusedElement: HTMLElement | null;

  constructor(public modal: IModal) {
    this.id = modal && modal.id;
    this.element = modal && modal.element;
    this.settings = modal && modal.settings;
    this.storageKey = modal && modal.storageKey;

    this._banishButton = this.element.querySelector('[class$="banish-button"]');
    this._closeButton = this.element.querySelector('[class$="close-button"]');

    this._firstFocusableElement = null;
    this._lastFocusableElement = null;
    this._lastFocusedElement = null;

    this._banishButton?.addEventListener('click', () => {
      this.hide();
      setModalTimeout(this.storageKey, -1);
    });

    this._closeButton?.addEventListener('click', () => {
      this.hide();
    });

    // Attach a keydown listener only when the modal isn't attached to the frame.
    if (this.settings.position.indexOf('frame') === -1) {
      this.element.addEventListener('keydown', this._handleKeyDown.bind(null, this));

      this.element.addEventListener('click', (event: MouseEvent) => {
        let clickTarget = event.target as HTMLElement;

        if (clickTarget.classList.contains('stcm-modal')) {
          this.hide();
        }
      });

      const FOCUSABLE_ELEMENTS = [
        ...this.element.querySelectorAll('input:not([type="hidden"])'),
        ...this.element.querySelectorAll('a'),
        ...this.element.querySelectorAll('button'),
      ];

      this._firstFocusableElement = FOCUSABLE_ELEMENTS[0] as HTMLElement;
      this._lastFocusableElement = FOCUSABLE_ELEMENTS[FOCUSABLE_ELEMENTS.length - 1] as HTMLElement;
    }
  }

  /**
   * Show the modal.
   */
  public show(): void {
    this._lastFocusedElement = document.activeElement as HTMLElement;

    setModalTimeout(this.storageKey, this.settings.frequency);

    // Focus on the first element only when the modal isn't attached to the frame.
    if (this.settings.position.indexOf('frame') === -1) {
      setTimeout(() => {
        this._firstFocusableElement?.focus();
      }, 500);
    }

    // Make the modal visible.
    this.element.setAttribute('aria-hidden', 'false');
    this.element.classList.remove('stcm-modal--hidden');
  }

  /**
   * Hide the modal.
   */
  public hide(): void {
    // Make the modal invisible.
    this.element.setAttribute('aria-hidden', 'true');
    this.element.classList.add('stcm-modal--hidden');

    if (this._lastFocusedElement) {
      this._lastFocusedElement.focus();
    }
  }

  private _handleKeyDown(modal: Modal, event: KeyboardEvent) {
    switch (event.code) {
      case 'Escape':
        modal.hide();
        break;

      case 'Tab':
        if (modal._firstFocusableElement && modal._lastFocusableElement) {
          // Tabbing backwards
          if (event.shiftKey) {
            if (document.activeElement === modal._firstFocusableElement) {
              event.preventDefault();
              modal._lastFocusableElement?.focus();
            }
          }
          // Tabbing forwards
          else {
            if (document.activeElement === modal._lastFocusableElement) {
              event.preventDefault();
              modal._firstFocusableElement?.focus();
            }
          }
        }
        break;

      default:
        break;
    }
  }
}

/**
 * Initializes all the modal using trigger "manual".
 */
class ManualModal extends Modal {
  constructor(
    public modal: IModal
  ) {
    super(modal);

    const TRIGGER_BUTTONS = document.querySelectorAll(`[aria-controls="${ this.id }"]`);

    if (TRIGGER_BUTTONS) {
      TRIGGER_BUTTONS.forEach((modalTriggerButton: Element) => {
        modalTriggerButton.addEventListener('click', () => {
          this.show();
        });
      });
    }
  }
}

/**
 * Initializes all the modal using trigger "scroll".
 */
class ScrollModal extends ManualModal {
  constructor(
    modal: IModal
  ) {
    super(modal);

    if (modalTimeoutExpired(this.storageKey)) {
      const scroll = () => {
        let scrollHeight = document.body.scrollHeight;
        let innerHeight = window.innerHeight;
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
        // Clamp value between 0 and 100
        let scrollPercentage = Math.round(Math.min(Math.max(0, (scrollTop / ( scrollHeight - innerHeight)) * 100), 100));

        if (this.settings.scroll_delay && (scrollPercentage >= this.settings.scroll_delay)) {
          this.show();

          window.removeEventListener('scroll', scroll);
        }
      }

      window.addEventListener('scroll', scroll);
    }
  }
}

/**
 * Initializes all the modal using trigger "timer".
 */
class TimerModal extends ManualModal {
  constructor(modal: IModal) {
    super(modal);

    if (this.settings.timer_delay) {
      setTimeout(() => {
        this.show();
      }, this.settings.timer_delay * 1000);
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const PARAGRAPH_MODALS = document.querySelectorAll('[data-paragraph-modal]');

  // **************************************************
  // SETUP EACH MODAL

  PARAGRAPH_MODALS.forEach((paragraphModal: Element) => {
    let modal: IModal = {
      id: paragraphModal.getAttribute('id'),
      element: paragraphModal as HTMLElement,
      settings: JSON.parse(paragraphModal.getAttribute('data-modal-settings') || '{}'),
      storageKey: `modal.${ paragraphModal.getAttribute('id') }`,
    };

    switch (modal.settings.trigger) {
      case 'manual':
        new ManualModal(modal);
        break;

      case 'scroll':
        new ScrollModal(modal);
        break;

      case 'timer':
        new TimerModal(modal);
        break;

      default:
        break;
    }
  });
});

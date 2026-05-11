(function () {
	'use strict';

	var storagePrefix = 'dspi_popup_closed_';

	function getPopupId(popup) {
		return popup.getAttribute('data-popup-id');
	}

	function readLocalStorage(key) {
		try {
			return window.localStorage.getItem(key);
		} catch (error) {
			return null;
		}
	}

	function writeLocalStorage(key, value) {
		try {
			window.localStorage.setItem(key, value);
		} catch (error) {
			// Storage may be unavailable in strict privacy modes.
		}
	}

	function readSessionStorage(key) {
		try {
			return window.sessionStorage.getItem(key);
		} catch (error) {
			return null;
		}
	}

	function writeSessionStorage(key, value) {
		try {
			window.sessionStorage.setItem(key, value);
		} catch (error) {
			// Storage may be unavailable in strict privacy modes.
		}
	}

	function getLocalRecord(key) {
		var raw = readLocalStorage(key);

		if (!raw) {
			return null;
		}

		try {
			return JSON.parse(raw);
		} catch (error) {
			return null;
		}
	}

	function isFrequencyAllowed(popup) {
		var id = getPopupId(popup);
		var frequency = popup.getAttribute('data-frequency') || 'always';
		var key = storagePrefix + id;
		var record;

		if (frequency === 'always') {
			return true;
		}

		if (frequency === 'session') {
			return readSessionStorage(key) !== '1';
		}

		record = getLocalRecord(key);
		if (!record || !record.expires) {
			return true;
		}

		return Date.now() > Number(record.expires);
	}

	function rememberClose(popup) {
		var id = getPopupId(popup);
		var frequency = popup.getAttribute('data-frequency') || 'always';
		var key = storagePrefix + id;
		var days = 1;

		if (frequency === 'always') {
			return;
		}

		if (frequency === 'session') {
			writeSessionStorage(key, '1');
			return;
		}

		if (frequency === 'custom') {
			days = Math.max(1, parseInt(popup.getAttribute('data-custom-days') || '1', 10));
		}

		writeLocalStorage(key, JSON.stringify({
			expires: Date.now() + days * 24 * 60 * 60 * 1000
		}));
	}

	function openPopup(popup, force) {
		if (!popup || (!force && !isFrequencyAllowed(popup))) {
			return;
		}

		popup.classList.remove('dspi-popup--hidden');
		popup.classList.add('dspi-popup--visible');
		popup.setAttribute('aria-hidden', 'false');

		var closeButton = popup.querySelector('[data-dspi-close]');
		if (closeButton) {
			closeButton.focus({ preventScroll: true });
		}
	}

	function closePopup(popup) {
		if (!popup) {
			return;
		}

		popup.classList.remove('dspi-popup--visible');
		popup.classList.add('dspi-popup--hidden');
		popup.setAttribute('aria-hidden', 'true');
		rememberClose(popup);
	}

	function findPopupFromTrigger(trigger) {
		var popupId = trigger.getAttribute('data-popupino-open-popup') || trigger.getAttribute('data-lcp-open-popup') || trigger.getAttribute('data-dspi-open-popup');
		var classMatch;
		var href;

		if (!popupId) {
			Array.prototype.some.call(trigger.classList, function (className) {
				classMatch = className.match(/^(?:popupino|lcp|dspi)-open-popup-(\d+)$/);
				if (classMatch) {
					popupId = classMatch[1];
					return true;
				}
				return false;
			});
		}

		if (!popupId && trigger.matches('a[href^="#popupino-popup-"], a[href^="#dspi-popup-"], a[href^="#lcp-popup-"]')) {
			href = trigger.getAttribute('href');
			popupId = href ? href.replace('#popupino-popup-', '').replace('#dspi-popup-', '').replace('#lcp-popup-', '') : '';
		}

		if (!popupId) {
			return null;
		}

		return document.querySelector('[data-dspi-popup][data-popup-id="' + popupId + '"]');
	}

	document.addEventListener('DOMContentLoaded', function () {
		var popups = document.querySelectorAll('[data-dspi-popup]');

		Array.prototype.forEach.call(popups, function (popup) {
			var delay = Math.max(0, parseInt(popup.getAttribute('data-delay') || '0', 10));

			popup.setAttribute('aria-hidden', 'true');

			window.setTimeout(function () {
				openPopup(popup, false);
			}, delay * 1000);
		});

		document.addEventListener('click', function (event) {
			var closeButton = event.target.closest('[data-dspi-close]');
			var trigger = event.target.closest('[data-popupino-open-popup], [data-lcp-open-popup], [data-dspi-open-popup], [class*="popupino-open-popup-"], [class*="lcp-open-popup-"], [class*="dspi-open-popup-"], a[href^="#popupino-popup-"], a[href^="#lcp-popup-"], a[href^="#dspi-popup-"]');
			var popup;

			if (closeButton) {
				event.preventDefault();
				closePopup(closeButton.closest('[data-dspi-popup]'));
				return;
			}

			if (event.target.matches('[data-dspi-popup].dspi-popup--visible')) {
				closePopup(event.target);
				return;
			}

			if (trigger) {
				popup = findPopupFromTrigger(trigger);
				if (popup) {
					event.preventDefault();
					openPopup(popup, true);
				}
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key !== 'Escape') {
				return;
			}

			Array.prototype.forEach.call(document.querySelectorAll('.dspi-popup--visible'), function (popup) {
				closePopup(popup);
			});
		});
	});
}());

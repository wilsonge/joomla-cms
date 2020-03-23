/**
 * @package     Joomla.Plugin
 * @subpackage  System.updatenotification
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Converts a simple object containing query string parameters to a single, escaped query string.
 * This method is a necessary evil since Joomla.request can only accept data as a string.
 *
 * @param    object   {object}  A plain object containing the query parameters to pass
 * @param    thisParamIsThePrefix   {string}  Prefix for array-type parameters
 *
 * @returns  {string}
 */
function plgSystemWebauthnInterpolateParameters(object, thisParamIsThePrefix) {
  const prefix = thisParamIsThePrefix || '';
  let encodedString = '';

  Object.keys(object).forEach((prop) => {
    if (typeof object[prop] !== 'object') {
      if (encodedString.length > 0) {
        encodedString += '&';
      }

      if (prefix === '') {
        encodedString += `${encodeURIComponent(prop)}=${encodeURIComponent(object[prop])}`;
      } else {
        encodedString
          += `${encodeURIComponent(prefix)}[${encodeURIComponent(prop)}]=${encodeURIComponent(
            object[prop],
          )}`;
      }

      return;
    }

    // Objects need special handling
    encodedString += `${plgSystemWebauthnInterpolateParameters(object[prop], prop)}`;
  });

  return encodedString;
}

/**
 * Finds the first field matching a selector inside a form
 *
 * @param   {HTMLFormElement}  elForm         The FORM element
 * @param   {String}           fieldSelector  The CSS selector to locate the field
 *
 * @returns {Element|null}  NULL when no element is found
 */
function plgSystemWebauthnFindField(elForm, fieldSelector) {
  const elInputs = elForm.querySelectorAll(fieldSelector);

  if (!elInputs.length) {
    return null;
  }

  return elInputs[0];
}

/**
 * Find a form field described by the CSS selector fieldSelector. The field must be inside a <form>
 * element which is either the outerElement itself or enclosed by outerElement.
 *
 * @param   {Element}  outerElement   The element which is either our form or contains our form.
 * @param   {String}   fieldSelector  The CSS selector to locate the field
 *
 * @returns {null|Element}  NULL when no element is found
 */
function plgSystemWebauthnLookForField(outerElement, fieldSelector) {
  const elElement = outerElement.parentElement;
  let elInput = null;

  if (elElement.nodeName === 'FORM') {
    elInput = plgSystemWebauthnFindField(elElement, fieldSelector);

    return elInput;
  }

  const elForms = elElement.querySelectorAll('form');

  if (elForms.length) {
    for (let i = 0; i < elForms.length; i += 1) {
      elInput = plgSystemWebauthnFindField(elForms[i], fieldSelector);

      if (elInput !== null) {
        return elInput;
      }
    }
  }

  return null;
}

/**
 * A simple error handler.
 *
 * @param   {String}  message
 */
function plgSystemWebauthnHandleLoginError(message) {
  alert(message);
}

/**
 * Handles the browser response for the user interaction with the authenticator. Redirects to an
 * internal page which handles the login server-side.
 *
 * @param {  Object}  publicKey     Public key request options, returned from the server
 * @param   {String}  callbackUrl  The URL we will use to post back to the server. Must include
 *   the anti-CSRF token.
 */
function plgSystemWebauthnHandleLoginChallenge(publicKey, callbackUrl) {
  function arrayToBase64String(a) {
    return btoa(String.fromCharCode(...a));
  }

  function base64url2base64(input) {
    let output = input
      .replace(/-/g, '+')
      .replace(/_/g, '/');
    const pad = output.length % 4;
    if (pad) {
      if (pad === 1) {
        throw new Error('InvalidLengthError: Input base64url string is the wrong length to determine padding');
      }
      output += new Array(5 - pad).join('=');
    }
    return output;
  }

  if (!publicKey.challenge) {
    plgSystemWebauthnHandleLoginError(Joomla.JText._('PLG_SYSTEM_WEBAUTHN_ERR_INVALID_USERNAME'));

    return;
  }

  publicKey.challenge = Uint8Array.from(
    window.atob(base64url2base64(publicKey.challenge)), c => c.charCodeAt(0),
  );

  if (publicKey.allowCredentials) {
    publicKey.allowCredentials = publicKey.allowCredentials.map((data) => {
      data.id = Uint8Array.from(window.atob(base64url2base64(data.id)), c => c.charCodeAt(0));
      return data;
    });
  }

  navigator.credentials.get({ publicKey })
    .then((data) => {
      const publicKeyCredential = {
        id: data.id,
        type: data.type,
        rawId: arrayToBase64String(new Uint8Array(data.rawId)),
        response: {
          authenticatorData: arrayToBase64String(new Uint8Array(data.response.authenticatorData)),
          clientDataJSON: arrayToBase64String(new Uint8Array(data.response.clientDataJSON)),
          signature: arrayToBase64String(new Uint8Array(data.response.signature)),
          userHandle: data.response.userHandle ? arrayToBase64String(
            new Uint8Array(data.response.userHandle),
          ) : null,
        },
      };

      // Send the response to your server
      window.location = `${callbackUrl}&option=com_ajax&group=system&plugin=webauthn&`
        + `format=raw&akaction=login&encoding=redirect&data=${
          btoa(JSON.stringify(publicKeyCredential))}`;
    })
    .catch((error) => {
      // Example: timeout, interaction refused...
      plgSystemWebauthnHandleLoginError(error);
    });
}

/**
 * Initialize the passwordless login, going through the server to get the registered certificates
 * for the user.
 *
 * @param   {string}   formId       The login form's or login module's HTML ID
 * @param   {string}   callbackUrl  The URL we will use to post back to the server. Must include
 *   the anti-CSRF token.
 *
 * @returns {boolean}  Always FALSE to prevent BUTTON elements from reloading the page.
 */
// eslint-disable-next-line no-unused-vars
function plgSystemWebauthnLogin(formId, callbackUrl) {
  // Get the username
  const elFormContainer = document.getElementById(formId);
  const elUsername = plgSystemWebauthnLookForField(elFormContainer, 'input[name=username]');
  const elReturn = plgSystemWebauthnLookForField(elFormContainer, 'input[name=return]');

  if (elUsername === null) {
    alert(Joomla.JText._('PLG_SYSTEM_WEBAUTHN_ERR_CANNOT_FIND_USERNAME'));

    return false;
  }

  const username = elUsername.value;
  const returnUrl = elReturn ? elReturn.value : null;

  // No username? We cannot proceed. We need a username to find the acceptable public keys :(
  if (username === '') {
    alert(Joomla.JText._('PLG_SYSTEM_WEBAUTHN_ERR_EMPTY_USERNAME'));

    return false;
  }

  // Get the Public Key Credential Request Options (challenge and acceptable public keys)
  const postBackData = {
    option: 'com_ajax',
    group: 'system',
    plugin: 'webauthn',
    format: 'raw',
    akaction: 'challenge',
    encoding: 'raw',
    username,
    returnUrl,
  };

  Joomla.request({
    url: callbackUrl,
    method: 'POST',
    data: plgSystemWebauthnInterpolateParameters(postBackData),
    onSuccess(rawResponse) {
      let jsonData = {};

      try {
        jsonData = JSON.parse(rawResponse);
      } catch (e) {
        /**
         * In case of JSON decoding failure fall through; the error will be handled in the login
         * challenge handler called below.
         */
      }

      plgSystemWebauthnHandleLoginChallenge(jsonData, callbackUrl);
    },
    onError: (xhr) => {
      plgSystemWebauthnHandleLoginError(`${xhr.status} ${xhr.statusText}`);
    },
  });

  return false;
}

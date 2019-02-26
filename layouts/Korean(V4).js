/**
 * @fileOverview Korean(V4) keyboard driver.
 * @author <a href="mailto:drachels@drachels.com">AL Rachels</a>
 * @version 4.1
 * @since 04/09/2018
 */

/**
 * Check for combined character.
 * @param {string} chr The combined character.
 * @returns {string} The character.
 */
function isCombined(chr) {
//alert('In the isCombined function');
    return true;
}

/**
 * Process keyup for combined character.
 * @param {string} e The combined character.
 * @returns {bolean} The result.
 */
function keyupCombined(e) {
//alert('In the keyupCombined function');
    return true;
}

/**
 * Process keyupFirst.
 * @param {string} event Type of event.
 * @returns {bolean} The event.
 */
function keyupFirst(event) {
//alert('In the keyupFirst function');
    return false;
}

/**
 * Check for character typed so flags can be set.
 * @param {string} ltr The current letter.
 */
function keyboardElement(ltr) {
//alert('In the keyboardElement(ltr) function and ltr is '+ltr);
    this.chr = ltr.toUpperCase();
    this.alt = false;
    if (ltr.match(/[~!@#$%^&*()_+]/i)) {
        this.shift = true;
    } else {
        this.shift = false;
    }
    this.turnOn = function() {
//alert('In the turnon function no check yet');
        if (isLetter(this.chr)) {
//alert('In the turnon function chcecking isLetter this.chr '+this.chr);
            document.getElementById(getKeyID(this.chr)).className = "next" + thenFinger(this.chr.toLowerCase());
//alert('In the turnon function chcecking isLetter this.chr after convert to lower case '+this.chr.toLowerCase());

        } else if (this.chr === ' ') {
//alert('In the turnon function chcecking isLetter this.chr is a space '+this.chr);
            document.getElementById(getKeyID(this.chr)).className = "nextSpace";
        } else {
            document.getElementById(getKeyID(this.chr)).className = "next" + thenFinger(this.chr.toLowerCase());
        }
        if (this.chr === '\n' || this.chr === '\r\n' || this.chr === '\n\r' || this.chr === '\r') {
            document.getElementById('jkeyenter').className = "next4";
        }
        if (this.shift) {
            document.getElementById('jkeyshiftd').className = "next4";
            document.getElementById('jkeyshiftl').className = "next4";
        }
        if (this.alt) {
            document.getElementById('jkeyaltgr').className = "nextSpace";
        }
    };
    this.turnOff = function() {
//alert('In the turnoff function no check yet');

        if (isLetter(this.chr)) {
//alert('In the turnoff function chcecking isLetter this.chr '+this.chr);

            if (this.chr.match(/[ㅁㄴㅇ러ㅏㅣ;]/i)) {
                document.getElementById(getKeyID(this.chr)).className = "finger" + thenFinger(this.chr.toLowerCase());
            } else {
                document.getElementById(getKeyID(this.chr)).className = "normal";
            }
        } else {
            document.getElementById(getKeyID(this.chr)).className = "normal";
        }
        if (this.chr === '\n' || this.chr === '\r\n' || this.chr === '\n\r' || this.chr === '\r') {
            document.getElementById('jkeyenter').classname = "normal";
        }
        if (this.shift) {
            document.getElementById('jkeyshiftd').className = "normal";
            document.getElementById('jkeyshiftl').className = "normal";
        }
        if (this.alt) {
            document.getElementById('jkeyaltgr').className = "normal";
        }
    };
}

/**
 * Set color flag based on current character.
 * @param {string} tCrka The current character.
 * @returns {number}.
 */
function thenFinger(tCrka) {
    if (tCrka === ' ') {
        return 5; // Highlight the spacebar.
    // @codingStandardsIgnoreLine
    } else if (tCrka.match(/[`~1!qㅂㅃaz0)pㅔㅖ;:/?\-_[{'"=+\]}\\|]/i)) {
        return 4; // Highlight the correct key above in red.
    // @codingStandardsIgnoreLine
    } else if (tCrka.match(/[2@wㅈㅉsx9(oㅐㅒl.>]/i)) {
        return 3; // Highlight the correct key above in green.
    // @codingStandardsIgnoreLine
    } else if (tCrka.match(/[3#eㄷㄸdc8*iㅑk,<]/i)) {
        return 2; // Highlight the correct key above in yellow.
    // @codingStandardsIgnoreLine
    } else if (tCrka.match(/[4$rㄱㄲfv5%tㅅㅆgb6^yㅛhn7&uㅕjm]/i)) {
        return 1; // Highlight the correct key above in blue.
    } else {
        return 6; // Do not change any highlight.
    }
}

/**
 * Get ID of key to highlight based on current character.
 * @param {string} tCrka The current character.
 * @returns {string}.
 */
function getKeyID(tCrka) {
//alert('In the function getKey and checking tCrka '+tCrka);

    if (tCrka === ' ') {
        return "jkeyspace";
    } else if (tCrka === ',') {
        return "jkeycomma";
    } else if (tCrka === '\n') {
        return "jkeyenter";
    } else if (tCrka === '.') {
        return "jkeyperiod";
    } else if (tCrka === '-' || tCrka === '_') {
        return "jkeyminus";
    } else if (tCrka === '`') {
        return "jkeybackquote";
    } else if (tCrka === '!') {
        return "jkey1";
    } else if (tCrka === '@') {
        return "jkey2";
    } else if (tCrka === '#') {
        return "jkey3";
    } else if (tCrka === '$') {
        return "jkey4";
    } else if (tCrka === '%') {
        return "jkey5";
    } else if (tCrka === '^') {
        return "jkey6";
    } else if (tCrka === '&') {
        return "jkey7";
    } else if (tCrka === '*') {
        return "jkey8";
    } else if (tCrka === '(') {
        return "jkey9";
    } else if (tCrka === ')') {
        return "jkey0";
    } else if (tCrka === '-' || tCrka === '_') {
        return "jkeyminus";
    } else if (tCrka === '[' || tCrka === '{') {
        return "jkeybracketl";
    } else if (tCrka === ']' || tCrka === '}') {
        return "jkeybracketr";
    } else if (tCrka === ';' || tCrka === ':') {
        return "jkeysemicolon";
    } else if (tCrka === "'" || tCrka === '"') {
        return "jkeycrtica";
    } else if (tCrka === "\\" || tCrka === '|') {
        return "jkeybackslash";
    } else if (tCrka === ',' || tCrka === '<') {
        return "jkeycomma";
    } else if (tCrka === '.' || tCrka === '>') {
        return "jkeyperiod";
    } else if (tCrka === '=' || tCrka === '+') {
        return "jkeyequals";
    } else if (tCrka === '?' || tCrka === '/') {
        return "jkeyslash";
    } else if (tCrka === '~' || tCrka === '`') {
        return "jkeybackquote";
    } else if (tCrka === 'q' || tCrka === 'ㅂ' || tCrka === 'ㅃ') {
        return "jkeyq";
    } else if (tCrka === 'w' || tCrka === 'ㅈ' || tCrka === 'ㅉ') {
        return "jkeyw";
    } else if (tCrka === 'e' || tCrka === 'ㄷ' || tCrka === 'ㄸ') {
        return "jkeye";
    } else if (tCrka === 'r' || tCrka === 'ㄱ' || tCrka === 'ㄲ') {
        return "jkeyr";
    } else if (tCrka === 't' || tCrka === 'ㅅ' || tCrka === 'ㅆ') {
        return "jkeyt";
    } else if (tCrka === 'y' || tCrka === 'ㅛ') {
        return "jkeyy";
    } else if (tCrka === 'u' || tCrka === 'ㅕ') {
        return "jkeyu";
    } else if (tCrka === 'i' || tCrka === 'ㅑ') {
        return "jkeyi";
    } else if (tCrka === 'o' || tCrka === 'ㅐ' || tCrka === 'ㅒ') {
        return "jkeyo";
    } else if (tCrka === 'p' || tCrka === 'ㅔ' || tCrka === 'ㅖ') {
        return "jkeyp";
    } else {
        return "jkey" + tCrka;
    }
}

/**
 * Is the typed letter part of the current alphabet.
 * @param {string} str The current letter.
 * @returns {(number|Array)}.
 */
function isLetter(str) {
//alert('In the function isLetter and checking str '+str);

    return str.length === 1 && str.match(/[!-ﻼ,ㅂㅈㄷㄱㄱ쇼ㅕㅑㅐ]/i);
    //return str.length === 1 && str.match);
    //return str.length === 1 && str.match(/[!-ﻼ]/i);

}

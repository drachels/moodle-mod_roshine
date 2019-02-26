function keyboardElement(ltr) {
    this.chr = ltr.toLowerCase();
    this.alt = false;
    if (isLetter(ltr)) { // quy định shift trái, shift phải - specified shift left, right shift
        if (ltr == 'Q' || ltr == 'W' || ltr == 'E' || ltr == 'R' || ltr == 'T' || 
            ltr == 'A' || ltr == 'S' || ltr == 'D' || ltr == 'F' || ltr == 'G' ||
            ltr == 'Z' || ltr == 'X' || ltr == 'C' || ltr == 'V' || ltr == 'B') {
            this.shiftright = true;
        } else if (ltr == 'Y' || ltr == 'U' || ltr == 'I' || ltr == 'O' || ltr == 'P' || 
            ltr == 'H' || ltr == 'J' || ltr == 'K' || ltr == 'L' ||
            ltr == 'N' || ltr == 'M') {
            this.shiftleft = true;
        }
    } else {
        if (ltr == '~' || ltr == '!' || ltr == '@' || ltr == '#' || ltr == '$' || ltr == '%' ) {
            this.shiftright = true;
        } else if (ltr == '^' || ltr == '&' || ltr == '(' || ltr == ')' || ltr == '*' || ltr == '_' || ltr == '+' || 
           ltr == ':' || ltr == '"' || ltr == '|' || ltr == '{' || ltr == '}' || ltr =='<' || ltr == '>' || ltr == '?') {
            this.shiftleft = true;
        }
    }
    this.turnOn = function () {
        if (isLetterNotNumber(this.chr)) {
            document.getElementById(dobiTipkoId(this.chr)).className = "next"+dobiFinger(this.chr.toLowerCase())+" font18";
            if (dobiLeftHand(this.chr.toLowerCase())) {
                document.getElementById('lefthand').src = "images/left"+dobiFinger(this.chr.toLowerCase())+".png";
                document.getElementById('righthand').src = "images/righthand.png";
            } else {
                document.getElementById('righthand').src = "images/right"+dobiFinger(this.chr.toLowerCase())+".png";
                document.getElementById('lefthand').src = "images/lefthand.png";
            }
        } else if (this.chr == ' ') {
            document.getElementById(dobiTipkoId(this.chr)).className = "nextSpace";
            document.getElementById('lefthand').src = "images/left5.png";
            document.getElementById('righthand').src = "images/right5.png";
        } else {
            document.getElementById(dobiTipkoId(this.chr)).className = "next"+dobiFinger(this.chr.toLowerCase());
            if (dobiLeftHand(this.chr.toLowerCase())) {
                document.getElementById('lefthand').src = "images/left"+dobiFinger(this.chr.toLowerCase())+".png";
                    document.getElementById('righthand').src = "images/righthand.png";
            } else {
                document.getElementById('righthand').src = "images/right"+dobiFinger(this.chr.toLowerCase())+".png";
                document.getElementById('lefthand').src = "images/lefthand.png";
            }
        }
        if (this.chr == '\n' || this.chr == '\r\n' || this.chr == '\n\r' || this.chr == '\r') {
            document.getElementById('jkeyenter').className = "nextEnter";
            document.getElementById('righthand').src = "images/right4.png";
            document.getElementById('lefthand').src = "images/lefthand.png";
        }
        if (this.shiftleft) {
            document.getElementById('jkeyshiftl').className="nextShift left";
            document.getElementById('lefthand').src = "images/left4.png";
        }
        if (this.shiftright) {
            document.getElementById('jkeyshiftd').className="nextShift";
            document.getElementById('righthand').src = "images/right4.png";
        }
        if (this.alt) {
            document.getElementById('jkeyaltgr').className="nextSpace";
        }
    };
    this.turnOff = function () {
        if (isLetter(this.chr)) {
            document.getElementById(dobiTipkoId(this.chr)).className = "key single";
        } else if (this.chr ==' ') {
            document.getElementById(dobiTipkoId(this.chr)).className = "key wide_5";
        } else if (this.chr == '\n' || this.chr == '\r\n' || this.chr == '\n\r' || this.chr == '\r') {
            document.getElementById('jkeyenter').className = "key wide_3";
        } else {
            document.getElementById(dobiTipkoId(this.chr)).className = "key";
        }
        if (this.chr == '\n' || this.chr == '\r\n' || this.chr == '\n\r' || this.chr == '\r') {
            document.getElementById('jkeyenter').classname = "key wide_3";
        }
        if (this.shiftleft) {
            document.getElementById('jkeyshiftl').className="key wide_4";
        }
        if (this.shiftright) {
            document.getElementById('jkeyshiftd').className="key wide_4";
        }
        if (this.alt) {
            document.getElementById('jkeyaltgr').className="key wide_5";
        }
    };
}
function dobiFinger(tCrka) // hàm trả về số thứ tự ngón tay , đã được quy định lại các phím đánh ký tự số 1234567890...
{
    if (tCrka === ' ') {
        return 5; // Highlight the spacebar.
    // @codingStandardsIgnoreLine
    } else if (tCrka.match(/[ºª\\1!|qaáz<>0=pñ\'?`^\[´¨{\-_¡¿+*\]ç}]/i)) {
        return 4; // Highlight the correct key above in red.
    // @codingStandardsIgnoreLine
    } else if (tCrka.match(/[2"@wsx9)oól.:]/i)) {
        return 3; // Highlight the correct key above in green.
    // @codingStandardsIgnoreLine
    } else if (tCrka.match(/[3·#eé€dc8(iík,;]/i)) {
        return 2; // Highlight the correct key above in yellow.    // @codingStandardsIgnoreLine
    } else if (tCrka.match(/[4$~rf5%€tgv6&¬yhnb7/uúüjm]/i)) {
        return 1; // Highlight the correct key above in blue.
    } else {
        return 6; // Do not change any highlight.
    }
}

function dobiLeftHand(tCrka) // kiểm tra phím được đánh bởi tay trái?
{
    if(tCrka == '~' || tCrka == '!' || tCrka == '@' || tCrka == '#' || tCrka == '$' || tCrka == '%' || 
        tCrka == '`' || tCrka == '1' ||  tCrka == '2' || tCrka == '3' || tCrka == '4' || tCrka == '5' || 
        tCrka == 'q' || tCrka == 'w' || tCrka == 'e' || tCrka == 'r' || tCrka == 't' || 
        tCrka == 'a' || tCrka == 's' || tCrka == 'd' || tCrka == 'f' || tCrka == 'g' ||
         tCrka == 'z' || tCrka == 'x' || tCrka == 'c' || tCrka == 'v' || tCrka == 'b')
         return true;
    else
        return false;
}

function dobiTipkoId(tCrka) {
    if (tCrka === ' ') {
        return "jkeyspace";
    } else if (tCrka === ',' || tCrka === ';') {
        return "jkeycomma";
    } else if (tCrka === '\n') {
        return "jkeyenter";
    } else if (tCrka === '.' || tCrka === ':') {
        return "jkeyperiod";
    } else if (tCrka === '-' || tCrka === '_') {
        return "jkeyminus";
    } else if (tCrka === '!' || tCrka === '|') {
        return "jkey1";
    } else if (tCrka === '"' || tCrka === '@') {
        return "jkey2";
    } else if (tCrka === '·' || tCrka === '#') {
        return "jkey3";
    } else if (tCrka === '$'|| tCrka === '~') {
        return "jkey4";
    } else if (tCrka === '%') {
        return "jkey5";
    } else if (tCrka === '&' || tCrka === '¬') {
        return "jkey6";
    } else if (tCrka === '/') {
        return "jkey7";
    } else if (tCrka === '(') {
        return "jkey8";
    } else if (tCrka === ')') {
        return "jkey9";
    } else if (tCrka === '=') {
        return "jkey0";
    } else if (tCrka === '`' || tCrka === '^' || tCrka === '[') {
        return "jkeylefttick";
    } else if (tCrka === '´' || tCrka === '¨' || tCrka === '{') {
        return "jkeyrighttick";
    } else if (tCrka === 'ç' || tCrka === '}') {
        return "jkeyç";
    } else if (tCrka === "'" || tCrka === '?') {
        return "jkeyapostrophe";
    } else if (tCrka === '*' || tCrka === '+' || tCrka === ']') {
        return "jkeyplus";
    } else if (tCrka === '<' || tCrka === '>') {
        return "jkeyckck";
    } else if (tCrka === 'º' || tCrka === 'ª' || tCrka === '\\') {
        return "jkeytilde";
    } else if (tCrka === '¿') {
        return 'jkey¡';
    } else if (tCrka === 'a' || tCrka === 'á') {
        return "jkeya";
    } else if (tCrka === 'e' || tCrka === 'é' || tCrka === '€') {
        return "jkeye";
    } else if (tCrka === 'i' || tCrka === 'í') {
        return "jkeyi";
    } else if (tCrka === 'o' || tCrka === 'ó') {
        return "jkeyo";
    } else if (tCrka === 'u' || tCrka === 'ú' || tCrka === 'ü') {
        return "jkeyu";
    } else {
        return "jkey" + tCrka;
    }
}

function isLetter(str) {
  return str.length === 1 && str.match(/[a-z]/i);
}
function isLetterNotNumber(str) {
  return str.length === 1 && str.match(/[a-z]/);
}


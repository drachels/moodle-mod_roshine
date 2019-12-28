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
function dobiFinger(t_crka) { // The function returns the number of the finger, which has been redefined by the number keys 123456.
    if (t_crka == ' ') {
        return 5;
    } else if (t_crka == 'q' || t_crka == 'a' ||  t_crka == 'z' ||  t_crka == '1' ||  t_crka == '!' ||  t_crka == '~' || t_crka == '`' ||
            t_crka == '0' || t_crka == ')' ||  t_crka == 'p' ||  t_crka == ':' ||  t_crka == ';' ||  t_crka == '?' || t_crka == '/' ||
            t_crka == '\'' || t_crka == '"' ||  t_crka == '[' ||  t_crka == ']' ||  t_crka == '{' ||  t_crka == '}' || t_crka == '|' ||
            t_crka == '\\' || t_crka == '-' ||  t_crka == '_' ||  t_crka == '+' ||  t_crka == '=') {
        return 4;
    } else if (t_crka == 'w' || t_crka == 's' || t_crka == 'x' || t_crka == '2' || t_crka == '@' || t_crka == '.' || t_crka == '>' ||
            t_crka == 'l' || t_crka == 'o' || t_crka == '9' || t_crka == '(') {
        return 3;
    } else if (t_crka == 'd' || t_crka == 'e' || t_crka == 'c' || t_crka == '3' || t_crka == '#' ||
            t_crka == 'k' || t_crka == '<' || t_crka == ',' || t_crka == 'i' || t_crka == '8' || t_crka == '*') {
        return 2;
    } else if (t_crka == 'v' || t_crka == 'b' || t_crka == 'f' || t_crka == 'g' || t_crka == 'r' || t_crka == 't' || 
            t_crka == '4' || t_crka == '5' || t_crka == '$' || t_crka == '%' ||  
            t_crka == '6' || t_crka == '7' || t_crka == '^' || t_crka == '&' || 
            t_crka == 'm' || t_crka == 'n' || t_crka == 'j' || t_crka == 'h' || t_crka == 'u' || t_crka == 'y') {
        return 1;
    } else {
        return 6;
    }
}

function dobiLeftHand(t_crka) { // Check the key is hit by the left hand?
    if (t_crka == '~' || t_crka == '!' || t_crka == '@' || t_crka == '#' || t_crka == '$' || t_crka == '%' || 
        t_crka == '`' || t_crka == '1' ||  t_crka == '2' || t_crka == '3' || t_crka == '4' || t_crka == '5' || 
        t_crka == 'q' || t_crka == 'w' || t_crka == 'e' || t_crka == 'r' || t_crka == 't' || 
        t_crka == 'a' || t_crka == 's' || t_crka == 'd' || t_crka == 'f' || t_crka == 'g' ||
         t_crka == 'z' || t_crka == 'x' || t_crka == 'c' || t_crka == 'v' || t_crka == 'b') {
         return true;
    } else {
        return false;
    }
}

function dobiTipkoId(t_crka) {
    if (t_crka == ' ')
        return "jkeyspace";
    else if (t_crka == ',')
        return "jkeyvejica";
    else if (t_crka == '\n')
        return "jkeyenter";
    else if (t_crka == '.')
        return "jkeypika";
    else if (t_crka == '-' || t_crka == '_')
        return "jkeypomislaj";            
    else if (t_crka == '!')
        return "jkey1";
    else if (t_crka == '@')
        return "jkey2";
    else if (t_crka == '#')
        return "jkey3";
    else if (t_crka == '$')
        return "jkey4";
    else if (t_crka == '%')
        return "jkey5";
    else if (t_crka == '^')
        return "jkey6";
    else if (t_crka == '&')
        return "jkey7";
    else if (t_crka == '*')
        return "jkey8";
    else if (t_crka == '(')
        return "jkey9";
    else if (t_crka == ')')
        return "jkey0";
    else if (t_crka ==  '-' || t_crka == '_')    
        return "jkeypomislaj";
    else if (t_crka == '[' || t_crka == '{')    
        return "jkeyoglokl";
    else if (t_crka == ']' || t_crka == '}')
        return "jkeyoglzak";
    else if (t_crka == ';' || t_crka == ':')
        return "jkeypodpicje";
    else if (t_crka == "'" || t_crka == '"')
        return "jkeycrtica";
    else if (t_crka == "\\" || t_crka == '|')
        return "jkeybackslash";
    else if (t_crka == ',')
        return "jkeyvejica";
    else if (t_crka == '.')
        return "jkeypika";
    else if (t_crka == '=' || t_crka == '+')
        return "jkeyequals";
    else if (t_crka == '?' || t_crka == '/')
        return "jkeyslash";
    else if (t_crka == '<' || t_crka == '>')
        return "jkeyckck";
    else if (t_crka == '`' || t_crka == '~') //Viet: mới thêm vào 13/02/2013
        return "jkeytildo";
    else
        return "jkey"+t_crka;
}

function isLetter(str) {
  return str.length === 1 && str.match(/[a-z]/i);
}
function isLetterNotNumber(str) {
  return str.length === 1 && str.match(/[a-z]/);
}


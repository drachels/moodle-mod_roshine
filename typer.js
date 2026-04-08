var startTime;
var endTime;
var error;
var currentPos;
var started = false;
var ended = false;
var currentChar;
var fullText;
var intervalID = -1;
var interval2ID = -1;
var app_url;
var show_keyboard;
var continuous_type = false;
var count_mistyped_spaces = false;
var count_all_mistakes = false;
var key_result = true;
var snd = new Audio("sounds/type.ogg");
var snd2 = new Audio("sounds/wrong.ogg");

var mistakestring = "";

function setAllById(id, value) {
    var nodes = document.querySelectorAll('[id="' + id + '"]');
    for (var i = 0; i < nodes.length; i++) {
        nodes[i].innerHTML = value;
    }
}

function setAllDisplayById(id, displayValue) {
    var nodes = document.querySelectorAll('[id="' + id + '"]');
    for (var i = 0; i < nodes.length; i++) {
        nodes[i].style.display = displayValue;
    }
}

function showRosModalById(modalid) {
    var modalel = document.getElementById(modalid);
    if (!modalel) {
        return;
    }
    if (typeof window.$ === 'function') {
        try {
            var modal = window.$('#' + modalid);
            if (modal && typeof modal.modal === 'function') {
                modal.modal('show');
            }
        } catch (ignore) {
            // Fall through to direct DOM fallback below.
        }
    }
    modalel.classList.remove('hide');
    modalel.classList.add('in');
    modalel.style.display = 'block';
    modalel.setAttribute('aria-hidden', 'false');
}

function renderTypingChar(ch) {
    if (ch === '\n') {
        return '&darr;';
    }
    if (ch === ' ') {
        return '&nbsp;';
    }
    return ch;
}

window.addEventListener('load', function() {
    focusSet();
});

function shouldIgnoreKeyDown(e) {
    var key = e && e.key ? e.key : '';
    return key === 'Shift' || key === 'Control' || key === 'Alt' || key === 'Meta' ||
        key === 'CapsLock' || key === 'Tab' || key === 'Escape' || key === 'AltGraph';
}

function isEnterTarget(ch) {
    return ch === '\n' || ch === '\r\n' || ch === '\n\r' || ch === '\r';
}

function isCharMatch(expected, typed) {
    if (typed === expected) {
        return true;
    }
    if (isEnterTarget(expected) && typed === ' ') {
        return true;
    }
    return false;
}

function markMistakeForCurrentChar() {
    error++;
    mistakestring += currentChar;
}

function shouldCountMistakeForWrongKey(keychar) {
    if (keychar === ' ' && !count_mistyped_spaces) {
        return false;
    }
    if (count_all_mistakes) {
        return true;
    }
    return key_result;
}

function advanceToNextPosition() {
    if (show_keyboard && typeof keyboardElement === 'function') {
        var thisE = new keyboardElement(currentChar);
        thisE.turnOff();
    }

    if (currentPos === fullText.length - 1) {
        doTheEnd();
        return true;
    }

    if (currentPos < fullText.length - 1) {
        var nextChar = fullText[currentPos + 1];
        if (show_keyboard && typeof keyboardElement === 'function') {
            var nextE = new keyboardElement(nextChar);
            nextE.turnOn();
        }
    }

    moveCursor(currentPos + 1);
    scrollit(currentPos + 1);
    currentChar = fullText[currentPos + 1];
    currentPos++;
    key_result = true;
    return true;
}

document.addEventListener('keydown', function(e) {
    if (ended) {
        return;
    }
    if (!document.form1 || !document.form1.tb1) {
        return;
    }
    if (shouldIgnoreKeyDown(e)) {
        return;
    }
    if (e.ctrlKey || e.altKey || e.metaKey) {
        return;
    }
    var handled = onUserPressKey(e);
    // Prevent default so the same key is not processed again by onkeypress.
    e.preventDefault();
});

/**
 * If not the end of fullText, move cursor to next character.
 * Color the previous character according to result.
 *
 * @param {number} nextPos Next cursor position.
 */
function moveCursor(nextPos) {
    if (nextPos > 0 && nextPos <= fullText.length) {
        if(document.getElementById('optSound').checked == true) snd.cloneNode(true).play(); // Play sound.
        document.getElementById('crka'+(nextPos-1)).className = "txtZeleno";
    }
        
    if (nextPos < fullText.length) {
        document.getElementById('crka'+(nextPos)).className = "txtModro";
    }
}

function scrollit(nextPos) {
    if (document.getElementById('crka'+(nextPos)).className == "txtModro" && document.getElementById('crka'+(nextPos)).offsetLeft <14)
        document.getElementById('textToEnter').scrollTop = document.getElementById('crka'+(nextPos)).offsetTop;
}

/**
 * End of typing.
 *
 */
function doTheEnd() {
    // Execute modalLessonComplete when exercise is done.
    showRosModalById('modalLessonComplete');
    setTimeout(function() {
        showRosModalById('modalLessonComplete');
    }, 75);
    if (typeof setOptionModalAttributes === 'function') {
        setOptionModalAttributes();
    }

    document.getElementById('crka'+(fullText.length-1)).className = "txtZeleno";
    ended = true;
    clearInterval(intervalID);
    clearInterval(interval2ID);
    endTime = new Date();
    differenceT = timeDifference(startTime, endTime);
    var hours = differenceT.getHours();
    var mins = differenceT.getMinutes();
    var secs = differenceT.getSeconds();
    var samoSekunde = converToSeconds(hours, mins, secs); 
    var speed = tinhtoanTocDo(samoSekunde);
    var acc = tinhDoChinhXac(fullText, error).toFixed(1);
    var accnum = parseFloat(acc);
    var goalnum = parseFloat(document.form1.rpGoal.value);
    document.form1.rpFullHits.value = (fullText.length + error);
    document.form1.rpTimeInput.value = samoSekunde;
    document.form1.rpMistakesInput.value = error;
    document.form1.rpAccInput.value = tinhDoChinhXac(fullText, error).toFixed(2);
    document.form1.rpSpeedInput.value = speed;
    document.form1.rpWpmInput.value = Math.max(0, (speed / 5) - error).toFixed(0);
    document.form1.tb1.disabled="disabled";    
    document.form1.btnContinue.style.visibility="visible";
    var request = makeHttpObject();
    var rpAttId = document.form1.rpAttId.value;
    var juri =  app_url+"/mod/roshine/atchk.php?status=3&attemptid="+rpAttId;
    request.open("GET", juri, true);
    request.send(null);

    setAllById('modalWPM', speed.toFixed(2));
    setAllById('modalAccuracy', acc + '%');
    mistakestring_calc();

    if (!isNaN(goalnum) && goalnum > 0 && !isNaN(accnum) && accnum < goalnum) {
        setAllDisplayById('modalError', 'block');
    } else {
        setAllDisplayById('modalError', 'none');
    }
}

/**
 * Get the character for the pressed key depending on current keyboard driver.
 *
 */
function getPressedChar(e) {
    var keynum;
    var keychar;
    if (e && typeof e.key === 'string') {
        if (e.key === 'Enter') {
            return '\n';
        }
        if (e.key.length === 1) {
            return e.key;
        }
    }
    if (window.event) { // IE
        keynum = e.keyCode;
    } else if (e.which) { // Netscape/Firefox/Opera
        keynum = e.which;
    }
    if (keynum == 13) {
        keychar = '\n';
    } else {
        keychar = String.fromCharCode(keynum);
    }
    return keychar;
}

/**
 * Set the focus.
 *
 */
function focusSet(e) {
    if (!started && show_keyboard && typeof keyboardElement === 'function' && fullText && fullText.length > 0) {
        var thisEl = new keyboardElement(fullText[0]);
        thisEl.turnOn();
    }
    return true;
}

/**
 * Do checks.
 *
 */
function doCheck() {
    var request = makeHttpObject();
    var rproshineId = document.form1.rpSityperId.value;
    var rpUser = document.form1.rpUser.value;
    var rpAttId = document.form1.rpAttId.value;
    var juri =  app_url+"/mod/roshine/atchk.php?status=2&attemptid="+rpAttId+"&mistakes="+error+"&hits="+(currentPos+error);
    request.open("GET", juri, true);
    request.send(null);
}

/**
 * Start exercise and reset data variables.
 *
 */
function doStart() {
    startTime = new Date();
    error = 0;
    mistakestring = "";
    currentPos = 0;
    started = true;
    key_result = true;
    currentChar = fullText[currentPos];
    intervalID = setInterval('updTimeSpeed()', 1000);
    var request = makeHttpObject();
    var rproshineId = document.form1.rpSityperId.value;
    var rpUser = document.form1.rpUser.value;
    var juri =  app_url+"/mod/roshine/atchk.php?status=1&roshineid="+rproshineId+"&userid="+rpUser+"&time="+(startTime.getTime()/1000);
    request.onreadystatechange=function()
    {
        if (request.readyState==4 && request.status==200)
            document.form1.rpAttId.value = request.responseText;
    }
    request.open("GET", juri, true);
    request.send(null);
    interval2ID = setInterval('doCheck()', 3000);
}

function makeHttpObject() {
    try {return new XMLHttpRequest();}
    catch (error) {}
    try {return new ActiveXObject("Msxml2.XMLHTTP");}
    catch (error) {}
    try {return new ActiveXObject("Microsoft.XMLHTTP");}
    catch (error) {}
    throw new Error("Could not create HTTP request object.");
}

/**
 * Process current key press and proceed based on typing mode.
 *
 */
function onUserPressKey(e) {
    if (ended)
        return false;
    if (!started){
        doStart();
    }
    var keychar = getPressedChar(e);
    if (isCharMatch(currentChar, keychar)) {
        key_result = true;
        return advanceToNextPosition();
    } else { // When typing the wrong key.
        if (document.getElementById('optSound').checked == true) snd2.cloneNode(true).play(); // play sound = khi go sai phim

        if (shouldCountMistakeForWrongKey(keychar)) {
            markMistakeForCurrentChar();
        }
        key_result = false;

        if (!continuous_type) {
            return false;
        }

        return advanceToNextPosition();
    }
}

/**
 * Calculate time to seconds.
 *
 */
function converToSeconds(hrs, mins, seccs) {
    if (hrs > 0) {
        mins = (hrs * 60) + mins;
    }
    if (mins === 0) {
        return seccs;
    } else {
        return (mins * 60) + seccs;
    }
}

/**
 * Calculate date difference.
 *
 */
function timeDifference(t1, t2) {
    var yrs = t1.getFullYear();
    var mnth = t1.getMonth();
    var dys = t1.getDate();
    var h1 = t1.getHours();
    var m1 = t1.getMinutes();
    var s1 = t1.getSeconds();
    var h2 = t2.getHours();
    var m2 = t2.getMinutes();
    var s2 = t2.getSeconds();
    var ure = h2 - h1;
    var minute = m2 - m1;
    var secunde = s2 - s1;
    return new Date(yrs, mnth, dys, ure, minute, secunde, 0);
}

/**
 * Initialize text to enter.
 *
 */
function initTypingText(ttext, tinprogress, tmistakes, thits, tstarttime, tattemptid, turl, tshowkeyboard) {
    var tcontinuoustype = arguments.length > 8 ? arguments[8] : false;
    var tcountmistypedspaces = arguments.length > 9 ? arguments[9] : false;
    var tcountmistakes = arguments.length > 10 ? arguments[10] : false;
    show_keyboard = tshowkeyboard;
    continuous_type = !!tcontinuoustype;
    count_mistyped_spaces = !!tcountmistypedspaces;
    count_all_mistakes = !!tcountmistakes;
    fullText = ttext;
    app_url = turl;
    var tempStr="";
    var resumedpos = (thits - tmistakes);
    // If resume position is out of range, do a clean start instead of a broken locked state.
    if (tinprogress && (resumedpos < 0 || resumedpos >= ttext.length)) {
        tinprogress = 0;
    }
    if(tinprogress){
        document.form1.rpAttId.value = tattemptid;
        startTime = new Date(tstarttime*1000);
        error = tmistakes;
        currentPos = resumedpos;   //!!!!!!!!!!!!!!!!!!!!!!!!!!!
        currentChar = fullText[currentPos];
        if(show_keyboard && typeof keyboardElement === 'function') {
            var nextE = new keyboardElement(currentChar);
            nextE.turnOn();
        }
        started = true;
        intervalID = setInterval('updTimeSpeed()', 1000);
        interval2ID = setInterval('doCheck()', 3000);
        for(var i=0; i<currentPos; i++)
        {
            var tChar = ttext[i];
            if(tChar == '\n') {
                tempStr += "<span id='crka"+i+"' class='txtZeleno'>" + renderTypingChar(tChar) + "</span><br>";
            } else {
                tempStr += "<span id='crka"+i+"' class='txtZeleno'>" + renderTypingChar(tChar) + "</span>";
            }
        }
        tempStr += "<span id='crka"+currentPos+"' class='txtModro'>" + renderTypingChar(currentChar) + "</span>";
        for(var j=currentPos+1; j<ttext.length; j++)
        {
            var tChar = ttext[j];
            if(tChar == '\n') {
                tempStr += "<span id='crka"+j+"' class='txtRdece'>" + renderTypingChar(tChar) + "</span><br>";
            } else {
                tempStr += "<span id='crka"+j+"' class='txtRdece'>" + renderTypingChar(tChar) + "</span>";
            }
        }
        document.getElementById('textToEnter').innerHTML = tempStr;
        document.getElementById('textToEnter').scrollTop = document.getElementById('crka'+(currentPos)).offsetTop; // cuon xuong vi tri dang = go roll down position is typing
    
    }
    else
    {
        for(var i=0; i<ttext.length; i++)
        {
            var tChar = ttext[i];

            if(i==0)
                tempStr += "<span id='crka"+i+"' class='txtModro'>" + renderTypingChar(tChar) + "</span>";
            else if(tChar == '\n')
                tempStr += "<span id='crka"+i+"' class='txtRdece'>" + renderTypingChar(tChar) + "</span><br>";
            else
                tempStr += "<span id='crka"+i+"' class='txtRdece'>" + renderTypingChar(tChar) + "</span>";
        }
        document.getElementById('textToEnter').innerHTML = tempStr;
    
    }
}
/**
 *
 * as the owner = laChuSo
 */
function laChuSo(aChar)
{
   myCharCode = aChar.charCodeAt(0);
   if((myCharCode > 47) && (myCharCode <  58))
   {
      return true;
   } 
   return false;
}

/**
 * Calculate speed.
 *
 * speed calculation = tinhtoanTocDo
 */
function tinhtoanTocDo(sc)
{
    return (((currentPos + error) * 60) / sc);
}

/**
 * Calculate accuracy.
 *
 * accuracy = tinhDoChinhXac
 */
function tinhDoChinhXac()
{
    if(currentPos+error == 0)
        return 0;
    return ((currentPos * 100) / (currentPos+error));
    
}

/**
 * Update current time, progress, mistakes presicsion, hits per minute, and words per minute.
 * speed calculation = tinhtoanTocDo
 * accuracy = tinhDoChinhXac
 */
function updTimeSpeed() {    
    newCas = new Date();
    tDifference = timeDifference(startTime, newCas);
    var secs = converToSeconds(tDifference.getHours(), tDifference.getMinutes(), tDifference.getSeconds());
    var speed = tinhtoanTocDo(secs);
    var wpm = (speed / 5) - error;
    document.getElementById('jsTime').innerHTML = secs;
    document.getElementById('jsSpeed').innerHTML = tinhtoanTocDo(fullText, error, secs).toFixed(2);
    document.getElementById('jsMistakes').innerHTML = mistakestring;
    document.getElementById('jsProgress').innerHTML = currentPos + "/" +fullText.length;
    document.getElementById('jsSpeed').innerHTML = tinhtoanTocDo(secs).toFixed(2);
    document.getElementById('jsAcc').innerHTML = tinhDoChinhXac(fullText, error).toFixed(1);
    mistakestring_calc();
    if(wpm>=0)
    {
    document.getElementById('jsWpm').innerHTML = wpm.toFixed(0);
    document.getElementById('jsWpm2').innerHTML = wpm.toFixed(0);
    }
    else{
        document.getElementById('jsWpm').innerHTML = 0;
        document.getElementById('jsWpm2').innerHTML = 0;
    }
    document.getElementById('jsTime2').innerHTML = tDifference.getMinutes()+':'+tDifference.getSeconds();
    document.getElementById('jsAcc2').innerHTML = tinhDoChinhXac(fullText, error).toFixed(1);
    document.getElementById('jsProgress2').innerHTML = currentPos + "/" +fullText.length;
}

function mistakestring_calc() {
    var details = DemSoKyTu(mistakestring);
    if (!details) {
        details = 'No mistakes';
    }
    setAllById('jsDetailMistake', details);
}

// Separation of characters = TachKyTu
function TachKyTu(str) {
    if (!str || str.length === 0) {
        return [];
    }
    var array = new Array();
    var k = 1 ;
    array[0] = str[0];
    
    for(var i = 1 ;    i<str.length ; i++){        
        for(var j = 0 ; j<=array.length ; j++){
            if( j == array.length ){
                array[k] = str[i] ;
                k++;    
            }
            if ( str[i] == array[j] ) break;    
        }
    }
    return array;
}

// Counting the sign = DemSoKyTu
// Separation of characters = TachKyTu
// result = ketqua
// night = dem <- number of mistakes for the current character
function DemSoKyTu(str) {
    if (!str || str.length === 0) {
        return '';
    }
    var arr = TachKyTu(str);
    
    var arrC = new Array();
    var ketqua = "" ;
    //alert(arr);
    for ( var j = 0 ; j<arr.length ; j++) {
        var dem = 0 ;
        for ( var i = 0 ; i< str.length ; i++ ) {
            if(str[i] == arr[j]) dem++;
        }
        
        //ketqua += '"' + arr[j] + '"' + ' = ' + dem  + '    ;\n' ;
//        ketqua += arr[j] + '=' + dem  + ', ' ;
//        ketqua += '"' + arr[j] + '"=' + dem  + ', ' ;
        ketqua += "'" + arr[j] + "'=" + dem  + ", " ;
    }
    return ketqua;
}
function APlayer(e) {
    if (!("music"in e && "title"in e.music && "author"in e.music && "url"in e.music && "pic"in e.music))throw"APlayer Error: Music, music.title, music.author, music.url, music.pic are required in options";
    if (null === e.element)throw"APlayer Error: element option null";
    this.isMobile = navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)|(android)|(webOS)/i), this.isMobile && (e.autoplay = !1);
    var a = {element: document.getElementsByClassName("aplayer")[0], narrow: !1, autoplay: !1, showlrc: !1};
    for (var t in a)a.hasOwnProperty(t) && !e.hasOwnProperty(t) && (e[t] = a[t]);
    this.option = e
}
APlayer.prototype.init = function () {

    function e(e) {
        var a = e || window.event, i = (a.clientX - t(m.bar)) / p;
        i = i > 0 ? i : 0, i = 1 > i ? i : 1, m.updateBar.call(m, "played", i, "width"), m.option.showlrc && m.updateLrc.call(m, parseFloat(m.playedBar.style.width) / 100 * m.audio.duration), m.element.getElementsByClassName("aplayer-ptime")[0].innerHTML = m.secondToTime(i * m.audio.duration)
    }

    function a() {
        document.removeEventListener("mouseup", a), document.removeEventListener("mousemove", e), m.audio.currentTime = parseFloat(m.playedBar.style.width) / 100 * m.audio.duration, m.play()
    }

    function t(e) {
        for (var a, t = e.offsetLeft, i = e.offsetParent; null !== i;)t += i.offsetLeft, i = i.offsetParent;
        return a = document.body.scrollLeft + document.documentElement.scrollLeft, t - a
    }

    function i(e) {
        for (var a, t = e.offsetTop, i = e.offsetParent; null !== i;)t += i.offsetTop, i = i.offsetParent;
        return a = document.body.scrollTop + document.documentElement.scrollTop, t - a
    }
    //分割歌词
    if (this.element = this.option.element, this.music = this.option.music, this.option.showlrc) {
        this.lrcTime = [], this.lrcLine = [];
        for (var l = this.element.getElementsByClassName("aplayer-lrc-content")[0].innerHTML, s = l.split(/\n/), n = /\[(\d{2}):(\d{2})\.(\d{2}|\d{3})]/, r = /](.*)$/, o = 0; o < s.length; o++) {
            //console.log(s); //打印歌词
            var d = n.exec(s[o]), c = r.exec(s[o]);
            d && c && (this.lrcTime.push(60 * parseInt(d[1]) + parseInt(d[2]) + parseInt(d[3]) / 100), this.lrcLine.push(c[1]))
            
        }
    }

    if (this.element.innerHTML = '<div class="aplayer-pic"><img src="' + this.music.pic + '"><div class="aplayer-button aplayer-pause aplayer-hide"><i class="demo-icon aplayer-icon-pause"></i></div><div class="aplayer-button aplayer-play"><i class="demo-icon aplayer-icon-play"></i></div></div><div class="aplayer-info"><div class="aplayer-music"><span class="aplayer-title">' + this.music.title + '</span><span class="aplayer-author"> - (＞﹏＜)加载中,好累的说...</span></div><div class="aplayer-lrc"><div class="aplayer-lrc-contents" style="transform: translateY(0);"></div></div><div class="aplayer-controller"><div class="aplayer-bar-wrap"><div class="aplayer-bar"><div class="aplayer-loaded" style="width: 0"></div><div class="aplayer-played" style="width: 0"><span class="aplayer-thumb"></span></div></div></div><div class="aplayer-time"><span class="aplayer-ptime">00:00</span> / <span class="aplayer-dtime">(oﾟ▽ﾟ)</span><div class="aplayer-volume-wrap"><i class="demo-icon aplayer-icon-volume-down"></i><div class="aplayer-volume-bar-wrap"><div class="aplayer-volume-bar"><div class="aplayer-volume" style="height: 80%"></div></div></div></div></div></div></div>', this.option.showlrc) {
        this.element.classList.add("aplayer-withlrc");
        var u = "";
        for (this.lrcContents = this.element.getElementsByClassName("aplayer-lrc-contents")[0], o = 0; o < this.lrcLine.length; o++)u += "<p>" + this.lrcLine[o] + "</p>";
           // console.log(this.element.getElementsByClassName("aplayer-lrc-contents"));
           
        this.lrcContents.innerHTML = u, this.lrcIndex = 0, this.lrcContents.getElementsByTagName("p")[0].classList.add("aplayer-lrc-current")
    }
    this.option.narrow && this.element.classList.add("aplayer-narrow"), this.audio = document.createElement("audio"), this.audio.src = this.music.url, this.audio.loop = !0, this.audio.preload = "metadata";
    var m = this;
    this.audio.addEventListener("durationchange", function () {
        1 !== m.audio.duration && (m.element.getElementsByClassName("aplayer-dtime")[0].innerHTML = m.secondToTime(m.audio.duration))
    }), this.audio.addEventListener("loadedmetadata", function () {
        m.element.getElementsByClassName("aplayer-author")[0].innerHTML = " - " + m.music.author, m.loadedTime = setInterval(function () {
            var e = m.audio.buffered.end(m.audio.buffered.length - 1) / m.audio.duration;
            m.updateBar.call(m, "loaded", e, "width"), 1 === e && clearInterval(m.loadedTime)
        }, 500)
    }), this.audio.addEventListener("error", function () {
        m.element.getElementsByClassName("aplayer-author")[0].innerHTML = " - 加载失败 ╥﹏╥"
    }), this.playButton = this.element.getElementsByClassName("aplayer-play")[0], this.pauseButton = this.element.getElementsByClassName("aplayer-pause")[0], this.playButton.addEventListener("click", function () {
        m.play.call(m)
    }), this.pauseButton.addEventListener("click", function () {
        m.pause.call(m)
    }), this.playedBar = this.element.getElementsByClassName("aplayer-played")[0], this.loadedBar = this.element.getElementsByClassName("aplayer-loaded")[0], this.thumb = this.element.getElementsByClassName("aplayer-thumb")[0], this.bar = this.element.getElementsByClassName("aplayer-bar")[0];
    var p;
    this.bar.addEventListener("click", function (e) {
        var a = e || window.event;
        p = m.bar.clientWidth;
        var i = (a.clientX - t(m.bar)) / p;
        m.updateBar.call(m, "played", i, "width"), m.element.getElementsByClassName("aplayer-ptime")[0].innerHTML = m.secondToTime(i * m.audio.duration), m.audio.currentTime = parseFloat(m.playedBar.style.width) / 100 * m.audio.duration
    }), this.thumb.addEventListener("mousedown", function () {
        p = m.bar.clientWidth, clearInterval(m.playedTime), document.addEventListener("mousemove", e), document.addEventListener("mouseup", a)
    }), this.audio.volume = .8, this.volumeBar = this.element.getElementsByClassName("aplayer-volume")[0];
    var y = this.element.getElementsByClassName("aplayer-volume-bar")[0], h = m.element.getElementsByClassName("aplayer-time")[0].getElementsByTagName("i")[0], v = 35;
    this.element.getElementsByClassName("aplayer-volume-bar-wrap")[0].addEventListener("click", function (e) {
        var a = e || window.event, t = (v - a.clientY + i(y)) / v;
        t = t > 0 ? t : 0, t = 1 > t ? t : 1, m.updateBar.call(m, "volume", t, "height"), m.audio.volume = t, m.audio.muted && (m.audio.muted = !1), 1 === t ? h.className = "demo-icon aplayer-icon-volume-up" : h.className = "demo-icon aplayer-icon-volume-down"
    }), h.addEventListener("click", function () {
        m.audio.muted ? (m.audio.muted = !1, h.className = 1 === m.audio.volume ? "demo-icon aplayer-icon-volume-up" : "demo-icon aplayer-icon-volume-down", m.updateBar.call(m, "volume", m.audio.volume, "height")) : (m.audio.muted = !0, h.className = "demo-icon aplayer-icon-volume-off", m.updateBar.call(m, "volume", 0, "height"))
    }), this.option.autoplay && this.play()
}, APlayer.prototype.play = function () {
    this.playButton.classList.add("aplayer-hide"), this.pauseButton.classList.remove("aplayer-hide"), this.audio.play();
    var e = this;
    this.playedTime = setInterval(function () {
        e.updateBar.call(e, "played", e.audio.currentTime / e.audio.duration, "width"), e.option.showlrc && e.updateLrc.call(e), e.element.getElementsByClassName("aplayer-ptime")[0].innerHTML = e.secondToTime(e.audio.currentTime)
    }, 100)
}, APlayer.prototype.pause = function () {
    this.pauseButton.classList.add("aplayer-hide"), this.playButton.classList.remove("aplayer-hide"), this.audio.pause(), clearInterval(this.playedTime)
}, APlayer.prototype.updateBar = function (e, a, t) {
    a = a > 0 ? a : 0, a = 1 > a ? a : 1, this[e + "Bar"].style[t] = 100 * a + "%"
}, APlayer.prototype.updateLrc = function (e) {
    if (e || (e = this.audio.currentTime), e < this.lrcTime[this.lrcIndex] || e >= this.lrcTime[this.lrcIndex + 1])for (var a = 0; a < this.lrcTime.length; a++)e >= this.lrcTime[a] && (!this.lrcTime[a + 1] || e < this.lrcTime[a + 1]) && (this.lrcIndex = a, this.lrcContents.style.transform = "translateY(" + 20 * -this.lrcIndex + "px)", this.lrcContents.getElementsByClassName("aplayer-lrc-current")[0].classList.remove("aplayer-lrc-current"), this.lrcContents.getElementsByTagName("p")[a].classList.add("aplayer-lrc-current"))
}, APlayer.prototype.secondToTime = function (e) {
    var a = function (e) {
        return 10 > e ? "0" + e : "" + e
    }, t = parseInt(e / 60), i = parseInt(e - 60 * t);
    return a(t) + ":" + a(i)
};
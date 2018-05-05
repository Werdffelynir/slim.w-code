








/* functions
 ---------------------------------------------------------- */

function linkConfirm(){
    if( !confirm('Delete this record?') ) return false;
}

function linkVoid(){
    return false;
}


/* DOM is ready, content loaded
 ---------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', domLoaded, false);
function domLoaded(event){


    (function() {
        /* vote functional
        ---------------------------------------------------------- */
        var vote = {
            plu: document.querySelector('.vote_plu'),
            min: document.querySelector('.vote_min'),
            num: document.querySelector('.vote_num'),
            dis: document.querySelector('.vote_widget')
        };
        if(vote.plu != undefined){
            var snippId = vote.plu.getAttribute('data-id');
            function voteSend(e){
                var xhr = new XMLHttpRequest();
                xhr.open('POST','/selectvote');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onloadend = function(e){

                    var res = Number(xhr.responseText);

                    if(typeof res === 'number' && !isNaN(res)){

                        if( res >= Number(vote.num.innerHTML) )
                            vote.dis.classList.add('vote_bg_add_plu');
                        else
                            vote.dis.classList.add('vote_bg_add_min');

                        vote.num.innerHTML = (res > 0) ? '+'+res : res;

                        setTimeout(function(){
                            vote.dis.classList.add('vote_bg_trans');
                        },500);
                        setTimeout(function(){
                            vote.dis.classList.remove('vote_bg_add_plu');
                            vote.dis.classList.remove('vote_bg_add_min');
                            vote.dis.classList.remove('vote_bg_trans');
                        },2500);
                    }
                };
                xhr.send("id="+snippId+"&value="+this.selectVote);

            }
            vote.plu.selectVote = +1;
            vote.min.selectVote = -1;
            vote.plu.addEventListener('click', voteSend, false);
            vote.min.addEventListener('click', voteSend, false);
        }



        /* КОММЕНТАРИИ. Disqus functional
         ---------------------------------------------------------- */
        // Предтвращения выполнения кода с локальной машины
        if(window.location.hostname != "w-code.loc") {
            var disqus_shortname = 'w-code';

            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        }






        /* ? functional
        ---------------------------------------------------------- */
        enableTip('.tipser');


        var codeElems = document.querySelectorAll('.highlight');
        if(codeElems){
            var codeElemsArr = Array.prototype.slice.call(codeElems);
            codeElemsArr.forEach(function(item){
                hljs.highlightBlock(item);
            });
        }


    })(); // Closed area



} // END DOMContentLoaded



/* visit buttons functional
 ---------------------------------------------------------- */
/*var btnDetect = document.querySelector('.btn_detect');
 var btnBlock = document.querySelector('.btn_detect');

 if(btnDetect){
 btnDetect.addEventListener('click', function(){
 var id = this.parentNode.parentNode.getAttribute('data-id');
 var ip = this.parentNode.parentNode.getAttribute('data-ip');
 console.log(id, ip);

 var xhr = new XMLHttpRequest();
 xhr.open('POST','/selectvote');
 xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
 xhr.onloadend = function(e){};
 xhr.send("id="+snippId+"&value="+this.selectVote);

 },false);
 }*/

function onDetect(elem, ip, id){

    //console.log(  );
    var elemDetected = elem.parentNode.parentNode.querySelector('.detected');

    var xhr = new XMLHttpRequest();
    xhr.open('POST','/visitsDetect');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onloadend = function( loadend ){
        if (loadend && xhr.status == 200){
            try{
                var data = JSON.parse(xhr.responseText);
                if (typeof data['geo'] === 'object'){
                    var tip = (function(){
                        var str = "<p>Страны : " + data['geo']['country'] + "</p>" +
                            "<p>Регион : " + data['geo']['region'] + "</p>" +
                            "<p>Город : " + data['geo']['city'] + "</p>" +
                            "<p>Провайдер : " + data['prv']['name_ripe'] + "</p>" +
                            "<p>Сайт провайдера : " + data['prv']['site'] + "</p>" +
                            "<p>Сеть провайдера : " + data['prv']['route'] + "</p>";
                        return str.replace(/'/,'\'').replace(/"/,'\"');
                    })();
                    elemDetected.innerHTML = data['geo']['country'] + '. ' + data['geo']['region'] + ', ' + data['geo']['city'];
                    elemDetected.setAttribute('data-tip', tip);
                }
                console.log(data);
                console.log(elemDetected);
            }catch(e){
                console.error( 'Error parse response data!' )
            }
        }
    };
    xhr.send("ip="+ip+"&id="+id);
}

function onBlock(elem, id){
    console.log( elem );
    console.log( id );
}

function enableTip(sel){
    var elems = document.querySelectorAll(sel);
    if(elems) {
        elems = Array.prototype.slice.call(elems);
        elems.map(function(item){
            item.addEventListener('click', function (eve){
                if (!tipserIsOpen){
                    tipserIsOpen = true;
                    tipser.style.display = 'block';
                    tipser.content.innerHTML = item.getAttribute('data-tip');
                    item.appendChild(tipser);
                }
            }, false);
        });
    }
}
var tipserIsOpen = false;

var tipser = (function(){
    var tip = document.createElement('div'),
        content = document.createElement('div'),
        close = document.createElement('div');
    tip.id = 'tipser';
    tip.content = content;
    tip.close = close;
    content.className = 'tip_content';
    content.extContent = '';
    close.className = 'tip_close';
    close.textContent = 'x';
    close.addEventListener('click', function (eve){
        if (tipserIsOpen){
            tipserIsOpen = false;
            tipser.style.display = 'none';
            eve.stopPropagation();
        }
    }, false);
    tip.appendChild(close);
    tip.appendChild(content);
    return tip;
})();



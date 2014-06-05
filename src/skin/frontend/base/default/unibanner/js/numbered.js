function banner_selection(centerdiv_id,m1,duration,total){
	if(m1==1)
		clearTimeout(time1);
	if(document.getElementById(centerdiv_id).style.display=="none"){
		var i;
	for(i=0;i<array1.length;i++){
		document.getElementById(array1[i]).style.display="none";
		document.getElementById(array1[i]+"1").className="";
		}
		fadeeffect(centerdiv_id,m1);
	}
}

function fadeeffect(it,m1){
	Effect.Fade(it, { duration: .9, from: 0.2, to: 1 });
	document.getElementById(it).style.display="inline";
	document.getElementById(it+"1").className="selected";
	if(m1==1)
	flag=it.charAt(7);
}
var time;
function divin(){
        clearTimeout(time1);
}